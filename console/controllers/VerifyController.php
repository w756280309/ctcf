<?php

namespace console\controllers;

use common\models\bank\BankCardUpdate;
use common\models\user\QpayBinding;
use common\models\user\User;
use Ding\DingNotify;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * 交易状态查询与更新.
 */
class VerifyController extends Controller
{
    //免密查询
    public function actionMianmi()
    {
        $users = User::find()->where(['idcard_status' => User::IDCARD_STATUS_PASS, 'mianmiStatus' => 0])->andWhere(['>', 'created_at', strtotime('-7 day')])->limit(3)->all();
        foreach ($users as $user) {
            $resp = Yii::$container->get('ump')->getUserInfo($user->epayUser->epayUserId);
            $rparr = $resp->toArray();
            if (isset($rparr['user_bind_agreement_list']) && false !== strpos($resp->get('user_bind_agreement_list'), 'ZTBB0G00')) {
                //免密投资
                User::updateAll(['mianmiStatus' => 1], 'id='.$user->id);
            }
        }
    }

    //绑卡\换卡查询
    public function actionBindcard()
    {
        $qpay = QpayBinding::find()
            ->where(['status' => [QpayBinding::STATUS_ACK, QpayBinding::STATUS_INIT]])
            ->andWhere(['>', 'created_at', strtotime('-1 day')])
            ->andWhere(['<', 'created_at', time() - 10 * 60])
            ->all();

        $update = BankCardUpdate::find()
            ->where(['status' => [BankCardUpdate::STATUS_ACCEPT, BankCardUpdate::STATUS_PENDING]])
            ->andWhere(['>', 'created_at', strtotime('-90 days')])
            ->all();
        $datas = ArrayHelper::merge($qpay, $update);
        foreach ($datas as $dat) {
            $resp = Yii::$container->get('ump')->getBindingTx($dat);
            if ($resp->isSuccessful()) {
                $user = $dat->user;
                if (is_null($user)) {
                    continue;
                }
                if (in_array($resp->get('tran_state'), ['2', '3'])) {
                    Yii::info('绑卡|换卡联动返回 ump_log user_bank_console user_id: ' . $user->id . '; ret_code:' . $resp->get('tran_state'), 'umplog');
                }

                if ('2' === $resp->get('tran_state')) {
                    //成功的
                    if ($dat instanceof QpayBinding) {
                        $user->bindCard($dat, $resp->toArray());
                    } elseif ($dat instanceof BankCardUpdate) {
                        $user->updateCard($dat, $resp->toArray());
                    }
                } elseif ('3' === $resp->get('tran_state')) {
                    //失败的
                    if ($dat instanceof QpayBinding) {
                        $msg = '用户[' . $user->id . ']，于' . date('Y-m-d H:i:s', $dat->created_at) . ' 进行【绑卡】操作，操作失败，卡号 ' . $dat->card_number . '，失败原因，定时任务主动请求联动，联动返回状态:' . $resp->get('tran_state');
                        QpayBinding::updateAll(['status' => QpayBinding::STATUS_FAIL], ['id' => $dat->id]);
                    } elseif ($dat instanceof BankCardUpdate) {
                        BankCardUpdate::updateAll(['status' => BankCardUpdate::STATUS_FAIL], ['id' => $dat->id]);
                        $msg = '用户[' . $user->id . ']，于' . date('Y-m-d H:i:s', $dat->created_at) . ' 进行【换卡】操作，操作失败，卡号 ' . $dat->cardNo . '，失败原因，定时任务主动请求联动，联动返回状态:' . $resp->get('tran_state');
                    }
                    if (isset($msg)) {
                        (new DingNotify('wdjf'))->sendToUsers($msg);
                        Yii::info($msg, 'user_log');
                    }
                }
            }
        }
    }
}
