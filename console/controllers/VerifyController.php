<?php

namespace console\controllers;

use common\models\bank\BankCardManager;
use common\models\bank\BankCardUpdate;
use common\models\user\QpayBinding;
use common\models\user\User;
use common\models\user\UserBanks;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * 交易状态查询与更新.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class VerifyController extends Controller
{
    public function actionMianmi()
    {
        $users = User::find()->where(['idcard_status' => User::IDCARD_STATUS_PASS, 'mianmiStatus' => 0])->all();
        foreach ($users as $user) {
            $resp = Yii::$container->get('ump')->getUserInfo($user->epayUser->epayUserId);
            $rparr = $resp->toArray();
            if (isset($rparr['user_bind_agreement_list']) && false !== strpos($resp->get('user_bind_agreement_list'), 'ZTBB0G00')) {
                //免密投资
                User::updateAll(['mianmiStatus' => 1], 'id='.$user->id);
            }
        }
    }

    public function actionBindcard()
    {
        $qpay = QpayBinding::find()->where(['status' => QpayBinding::STATUS_ACK])->andWhere(['>', 'created_at', strtotime('-1 day')])->all();
        $update = BankCardUpdate::find()->where(['status' => QpayBinding::STATUS_ACK])->andWhere(['>', 'created_at', strtotime('-30 days')])->all();
        $datas = ArrayHelper::merge($qpay, $update);
        foreach ($datas as $dat) {
            $resp = Yii::$container->get('ump')->getBindingTx($dat);
            if ($resp->isSuccessful()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ('2' === $resp->get('tran_state')) {
                        //成功的
                        if ($dat instanceof QpayBinding) {
                            $dat->status = QpayBinding::STATUS_SUCCESS;
                            $data = ArrayHelper::toArray($dat);
                            unset($data['id']);
                            unset($data['status']);
                            $userBanks = new UserBanks($data);
                            $userBanks->setScenario('step_first');
                            if (!$userBanks->save() || !$dat->save()) {
                                throw new \Exception('处理失败');
                            }
                        } elseif ($dat instanceof BankCardUpdate) {
                            $dat->status = BankCardUpdate::STATUS_SUCCESS;    //更新换卡申请表记录状态
                            if (!$dat->save()) {
                                throw new \Exception('处理失败');
                            }
                            $bankCardMgr = new BankCardManager();
                            $bankCardMgr->confirmUpdate($dat); // 返回新卡的记录
                        }
                    } elseif ('3' === $resp->get('tran_state')) {
                        //失败的
                        if ($dat instanceof QpayBinding) {
                            QpayBinding::updateAll(['status' => 2], 'id='.$dat->id);
                        } elseif ($dat instanceof BankCardUpdate) {
                            BankCardUpdate::updateAll(['status' => 2], 'id='.$dat->id);
                        }
                    }
                    $transaction->commit();
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                }
            }
        }
    }
}
