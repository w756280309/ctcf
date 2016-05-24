<?php

namespace api\modules\v1\controllers\notify;

use common\models\payment\PaymentLog;
use common\models\TradeLog;
use Exception;
use Yii;
use yii\web\Controller;

/**
 * 贴现通知地址.
 */
class PaymentController extends Controller
{
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = 'no error';
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'project_tranfer_notify' === $data['service']
        ) {
            $order = PaymentLog::findOne(['sn' => $data['order_id']]);
            if ($order) {
                $err = "0000";
            } else {
                $errmsg = "无法找到log记录";
            }
        } else {
            $errmsg = '签名错误,或者操作失败';
        }

        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}