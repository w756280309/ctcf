<?php
/**
 * Created by ShiYang.
 * Date: 17-1-6
 * Time: 下午3:53
 */

namespace common\action\notify;

use common\models\TradeLog;
use common\models\user\QpayBinding;
use common\models\user\User;
use Yii;
use yii\base\Action;

//绑卡后台通知地址
class BankBackNotifyAction extends Action
{
    public function run()
    {
        $this->controller->layout = false;
        $err = '00009999';
        $data = Yii::$app->request->get();
        if (defined('IN_APP') && array_key_exists('token', $data)) {
            unset($data['token']);
        }
        TradeLog::initLog(2, $data, $data['sign'])->save();
        $bind = QpayBinding::findOne(['binding_sn' => $data['order_id']]);
        if (is_null($bind)) {
            throw new \Exception('无法找到记录');
        }
        $user = User::findOne($bind->uid);
        if (is_null($user)) {
            throw new \Exception('无法找到记录');
        }
        Yii::info('绑卡后台通知 ump_log user_bank_back_notify user_id: ' . $user->id . ';cardNo:.' . $bind->card_number .  '; ret_code:', 'umplog');
        if (
            Yii::$container->get('ump')->verifySign($data)
            && 'mer_bind_card_notify' === $data['service']
        ) {
            if ('0000' === $data['ret_code']) {
                if ($user->bindCard($bind, $data)) {
                    $err = '0000';
                }
            } else {
                $bind->status = QpayBinding::STATUS_FAIL;
                $bind->save(false);
                $err = '0000';
            }
        }

        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->controller->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}