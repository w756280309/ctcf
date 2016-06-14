<?php

namespace frontend\modules\order\controllers;

use common\models\contract\ContractTemplate;
use common\models\coupon\UserCoupon;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\product\OnlineProduct;
use common\service\PayService;
use EBaoQuan\Client;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;

class OrderController extends BaseController
{
    /**
     * 购买标的.
     */
    public function actionDoorder($sn)
    {
        if (empty($sn)) {
            throw new NotFoundHttpException();   //判断参数无效时,抛404异常
        }
        $money = \Yii::$app->request->post('money');
        $coupon_id = \Yii::$app->request->post('couponId');
        $coupon = null;
        if ($coupon_id) {
            $coupon = UserCoupon::findOne($coupon_id);
            if (null === $coupon) {
                return ['code' => 1,  'message' => '无效的代金券'];
            }
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($this->getAuthedUser(), $sn, $money, $coupon);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        $orderManager = new OrderManager();

        return $orderManager->createOrder($sn, $money,  $this->getAuthedUser()->id, $coupon);
    }

    /**
     * 认购标的结果页.
     */
    public function actionOrdererror($osn)
    {
        if (empty($osn)) {
            throw new NotFoundHttpException();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);
        $deal = null;
        if (null  !== $order && 1 !== $order->status) {
            $deal = OnlineProduct::findOne($order->online_pid);
        }
        if (\Yii::$app->request->isAjax) {
            return ['status' => $order->status];
        }

        return $this->render('error', ['order' => $order, 'deal' => $deal, 'ret' => (null  !== $order && 1 === $order->status) ? 'success' : 'fail']);
    }

    /**
     * 认购标的中间处理页.
     */
    public function actionOrderwait($osn)
    {
        if (empty($osn)) {
            throw new NotFoundHttpException();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);
        if (OnlineOrder::STATUS_FALSE  !== $order->status) {
            return $this->redirect('/order/order/ordererror?osn='.$order->sn);
        }

        return $this->render('wait', ['order' => $order]);
    }

    /**
     * 合同页面.
     */
    public function actionAgreement($pid)
    {
        if (empty($pid)) {
            $this->ex404();
        }

        $model = ContractTemplate::findAll(['pid' => $pid]);
        if (empty($model)) {
            $this->ex404();  //当对象为空时,抛出异常
        }

        $orderId = \Yii::$app->request->get('order_id');
        if (!empty($orderId) && !preg_match('/^[0-9]+$/', $orderId)) {
            $this->ex404();
        }

        if (empty($orderId)) {
            $order = null;
        } else {
            $order = OnlineOrder::findOne($orderId);
        }

        $ebao = [];
        foreach ($model as $key => $val) {
            $model[$key] = ContractTemplate::replaceTemplate($val, $order);

            //获取证书
            $baoQuan = EbaoQuan::findOne(['type' => $key, 'orderId' => $orderId, 'uid' => $this->getAuthedUser()->id]);
            if (null !== $baoQuan) {
                $client = new Client();
                $ebao[$key]['downUrl'] = $client->contractFileDownload($baoQuan);
                $ebao[$key]['linkUrl'] = $client->certificateLinkGet($baoQuan);
            }
        }

        return $this->render('agreement', [
            'model' => $model,
            'ebao' => $ebao,
        ]);
    }
}
