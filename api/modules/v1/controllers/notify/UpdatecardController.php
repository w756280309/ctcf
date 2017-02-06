<?php

namespace api\modules\v1\controllers\notify;

use common\models\bank\BankCardUpdate;
use common\models\TradeLog;
use common\models\user\User;
use Yii;
use yii\web\Controller;

class UpdatecardController extends Controller
{
    /**
     * 换卡申请页面前台回调函数.
     */
    public function actionFrontend()
    {
        $data = \Yii::$app->request->get();
        $channel = $this->getChannel($data);
        $isSucc = false;

        TradeLog::initLog(2, $data, $data['sign'])->save();    //记录交易日志信息
        $model = BankCardUpdate::findOne(['sn' => $data['order_id']]);
        if (!is_null($model)) {
            $user = User::findOne($model->uid);
        }
        if (
            !is_null($user)
            && \Yii::$container->get('ump')->verifySign($data)
            && 'mer_bind_card_apply_notify' === $data['service']
            && '0000' === $data['ret_code']
        ) {
            Yii::info('换卡前台回调 ump_log user_bank_update_front_notify user_id: ' . $user->id . ';cardNo:.' . $model->cardNo . ';mobile:' . $user->mobile . '; ret_code:' . $data['ret_code'] . ';ret_msg:' . $data['ret_msg'], 'umplog');

            if (BankCardUpdate::STATUS_PENDING === $model->status) {
                $model->status = BankCardUpdate::STATUS_ACCEPT;
                $model->save();
                $isSucc = true;
                Yii::info('用户信息变更日志 换卡 变更表:bank_card_update;变更属性:' . (json_encode(['status' => BankCardUpdate::STATUS_ACCEPT])) . ';user_id:' . $user->id . ';mobile:' . $user->mobile . ';变更依据:联动ret_code ' . $data['ret_code'] . ';联动返回信息:' . json_encode($data), 'user_log');
            }
        }

        $backUrl = $this->getBackUrl($channel, $isSucc);

        return $this->redirect($backUrl);
    }

    /**
     * 换卡申请页面后台回调函数.
     */
    public function actionBackend()
    {
        $data = \Yii::$app->request->get();
        $channel = $this->getChannel($data);

        $this->layout = false;
        $err = '0000';

        TradeLog::initLog(2, $data, $data['sign'])->save();  //记录交易日志
        $model = BankCardUpdate::findOne(['sn' => $data['order_id']]);
        if (is_null($model)) {
            throw new \Exception('无法找到记录');
        }
        $user = User::findOne($model->uid);
        if (is_null($user)) {
            throw new \Exception('无法找到记录');
        }
        Yii::info('换卡后台通知 ump_log user_bank_update_back_notify user_id: ' . $user->id . ';cardNo:.' . $model->cardNo . ';mobile:' . $user->mobile . '; ret_code:' . $data['ret_code'] . ';ret_msg:' . $data['ret_msg'], 'umplog');
        if (\Yii::$container->get('ump')->verifySign($data)) {
            if ('mer_bind_card_apply_notify' === $data['service']) {    //换卡申请后台通知
                $isSucc = false;
                if ('0000' === $data['ret_code']) {
                    if (BankCardUpdate::STATUS_PENDING === $model->status) {
                        $model->status = BankCardUpdate::STATUS_ACCEPT;
                        $model->save();
                        Yii::info('用户信息变更日志 换卡 变更表:bank_card_update;变更属性:' . (json_encode(['status' => BankCardUpdate::STATUS_ACCEPT])) . ';user_id:' . $user->id . ';mobile:' . $user->mobile . ';变更依据:联动ret_code ' . $data['ret_code'] . ';联动返回信息:' . json_encode($data), 'user_log');
                    }

                    $isSucc = true;
                }

                $backUrl = $this->getBackUrl($channel, $isSucc);

                return $this->redirect($backUrl);
            } elseif ('mer_bind_card_notify' === $data['service']) {    //换卡结果后台通知
                if ('0000' === $data['ret_code']) {
                    $res = $user->updateCard($model, $data);
                    if (!$res) {
                        $err = '00009999';
                    }
                } else {
                    $model->status = BankCardUpdate::STATUS_FAIL;
                    if (!$model->save()) {
                        $err = '00009999';
                    }
                }
            } else {
                $err = '00009999';
            }
        } else {
            $err = '00009999';
        }

        $content = \Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    private function getChannel(&$data)
    {
        if (array_key_exists('channel', $data) && 'pc' === $data['channel']) {
            unset($data['channel']);

            return 'pc';
        }

        if (array_key_exists('token', $data) && $data['token']) {
            unset($data['token']);

            return 'app';
        }

        return 'wap';
    }

    private function getBackUrl($channel, $isSucc)
    {
        if ('pc' === $channel) {
            $host = \Yii::$app->params['clientOption']['host']['frontend'];
            if ($isSucc) {
                return $host . 'info/success?source=huanka&jumpUrl=/user/bank/card';
            }

            return $host . 'info/fail?source=huanka';
        }

        if ('app' === $channel) {
            $host = \Yii::$app->params['clientOption']['host']['app'];
        } else {
            $host = \Yii::$app->params['clientOption']['host']['wap'];
        }

        if ($isSucc) {
            return $host . 'user/userbank/updatecardnotify?ret=success';
        }

        return $host . 'user/userbank/updatecardnotify';
    }
}
