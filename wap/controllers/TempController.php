<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use common\models\user\QpayBinding;
use common\models\TradeLog;

/**
 * 临时文件.
 */
class TempController extends Controller
{
    public function actionReplacecard($mobile)
    {
        $user = \common\models\user\User::findOne(['mobile' => $mobile]);
        $qpay = new QpayBinding([
            'binding_sn' => \common\utils\TxUtils::generateSn('B'),
            'uid' => $user->id,
            'epayUserId' => $user->epayUser->epayUserId,
            'bank_id' => $user->qpay->bank_id,
            'bank_name' => $user->qpay->bank_name,
            'account' => $user->qpay->account,
            'card_number' => $user->qpay->card_number,
            'account_type' => 11,
            'status' => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $next = \Yii::$container->get('ump')->changeQpay($qpay);

        return $this->redirect($next);
    }

    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (Yii::$container->get('ump')->verifySign($data) && '0000' === $data['ret_code']) {
            return $this->redirect('/');//成功跳首页
        } else {
            echo 'error';
            exit;
        }
    }

    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $data = Yii::$app->request->get();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && 'mer_bind_card_notify' === $data['service']
        ) {
            if ('0000' === $data['ret_code']) {
                $err = '0000';
            }
            $content = Yii::$container->get('ump')->buildQuery([
                    'order_id' => $data['order_id'],
                    'mer_date' => $data['mer_date'],
                    'reg_code' => $err,
                ]);

            return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
        }
    }
}
