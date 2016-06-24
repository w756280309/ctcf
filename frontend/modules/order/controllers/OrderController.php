<?php

namespace frontend\modules\order\controllers;

use common\models\contract\ContractTemplate;
use common\models\coupon\UserCoupon;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\service\PayService;
use EBaoQuan\Client;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;


class OrderController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [    //登录控制,如果没有登录,则跳转到登录页面
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 生成订单
     */
    public function actionDoorder($sn)
    {
        if (empty($sn)) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }
        $money = \Yii::$app->request->post('money');
        $coupon_id = \Yii::$app->request->post('couponId');
        $coupon = null;
        if ($coupon_id) {
            $coupon = UserCoupon::findOne($coupon_id);
            if (null === $coupon) {
                return ['code' => 1, 'message' => '无效的代金券'];
            }
        }

        $user = $this->getAuthedUser();
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($user, $sn, $money, $coupon, 'pc');
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        //下订单之前删除保存在session中的购买数据
        if (Yii::$app->session->has('detail_' . $sn . '_data')) {
            Yii::$app->session['detail_' . $sn . '_data'] = null;
        }
        $orderManager = new OrderManager();

        return $orderManager->createOrder($sn, $money, $user->id, $coupon);
    }

    /**
     * 认购标的中间处理页.
     */
    public function actionOrderwait($osn)
    {
        if (empty($osn)) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);
        if (OnlineOrder::STATUS_FALSE !== $order->status) {
            return $this->redirect('/info/success?source=touzi&jumpUrl=/licai/index');
        }

        return $this->render('wait', ['order' => $order]);
    }

    /**
     * 合同页面.
     */
    public function actionAgreement($pid)
    {
        if (empty($pid)) {
            throw $this->ex404();
        }

        $model = ContractTemplate::findAll(['pid' => $pid]);
        if (empty($model)) {
            throw $this->ex404();  //当对象为空时,抛出异常
        }

        $orderId = \Yii::$app->request->get('order_id');
        if (!empty($orderId) && !preg_match('/^[0-9]+$/', $orderId)) {
            throw $this->ex404();
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

    /**
     * 认购标的结果页
     */
    public function actionOrdererror($osn)
    {
        $order = OnlineOrder::ensureOrder($osn);
        if (\Yii::$app->request->isAjax) {
            return ['status' => $order->status];
        }
    }
}
