<?php
/**
 * Created by ShiYang.
 * Date: 17-1-6
 * Time: 下午3:31
 */

namespace common\action\notify;

use common\models\TradeLog;
use common\models\user\QpayBinding;
use common\models\user\User;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;

//绑卡前台回跳action
class BankFrontNotifyAction extends Action
{
    public function run()
    {
        $data = Yii::$app->request->get();
        if (defined('IN_APP') && array_key_exists('token', $data)) {
            unset($data['token']);
        }
        TradeLog::initLog(2, $data, $data['sign'])->save();
        $bind = QpayBinding::findOne(['binding_sn' => $data['order_id']]);
        if (is_null($bind)) {
            throw new NotFoundHttpException($data['order_id'] . ':无法找到申请数据');
        }
        if (CLIENT_TYPE === 'pc') {
            $successUrl = '/info/success?source=bangka&jumpUrl=' . urlencode('/user/bank/card');
            $errorUrl = '/info/fail?source=bangka';
        } else {
            $successUrl = '/user/userbank/accept?ret=success';
            $errorUrl = '/user/userbank/accept';
        }
        $user = User::findOne($bind->uid);
        if (is_null($user)) {
            throw new NotFoundHttpException('无法找到用户');
        }
        Yii::info('绑卡前台回跳 ump_log user_bank_front_notify user_id: ' . $user->id . ';cardNo:.' . $bind->card_number .  '; ret_code:' . $data['ret_code'] . ';ret_msg:' . $data['ret_msg'], 'umplog');
        if (Yii::$container->get('ump')->verifySign($data) && '0000' === $data['ret_code']) {
            if (QpayBinding::STATUS_INIT === $bind->status) {
                $bind->status = QpayBinding::STATUS_ACK;//处理中
                if ($bind->save(false)) {
                    Yii::info('用户信息变更日志 绑卡 变更表:qpaybinding;变更属性:' . (json_encode(['status' => $bind->status])) . ';user_id:' . $user->id .  ';变更依据:联动ret_code ' . $data['ret_code'] . ';联动返回信息:' . json_encode($data), 'user_log');
                    $redirectUrl = $successUrl;
                } else {
                    $redirectUrl = $errorUrl;
                }
            } else if (QpayBinding::STATUS_ACK === $bind->status) {
                $redirectUrl = $successUrl;
            } else {
                $redirectUrl = $errorUrl;
            }
        } else {
            $redirectUrl = $errorUrl;
        }

        return $this->controller->redirect($redirectUrl);
    }
}