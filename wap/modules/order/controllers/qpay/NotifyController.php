<?php

namespace app\modules\order\controllers\qpay;

use Yii;
use Exception;
use yii\web\Controller;
use common\service\OrderService;
use common\models\TradeLog;
use common\models\order\OnlineOrder;

/**
 * 联动优势投标
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class NotifyController extends Controller
{
    /**
     * 联动优势投标前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        $this->processing($data);
         return $this->redirect('/user/user/myorder');
    }

    /**
     *
     * @param array $data
     * @return type
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function processing(array $data = [])
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'project_tranfer_notify' === $data['service']
        ) {
            $order = OnlineOrder::findOne(['sn' => $data['order_id']]);
            OrderService::confirmOrder($order);
        } else {
            throw new Exception('交易失败');
        }
    }

    /**
     * 联动优势投标后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        $this->processing($data);
        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}
