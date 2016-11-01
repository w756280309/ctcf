<?php

namespace frontend\modules\order\controllers;

use common\controllers\ContractTrait;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\service\PayService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;


class OrderController extends BaseController
{
    use ContractTrait;

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
        //记录订单来源
        $investFrom = OnlineOrder::INVEST_FROM_PC;
        if ($this->fromWx()) {
            $investFrom = OnlineOrder::INVEST_FROM_WX;
        }
        return $orderManager->createOrder($sn, $money, $user->id, $coupon, $investFrom);
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
            if (OnlineOrder::STATUS_SUCCESS === $order->status) {
                return $this->redirect('/info/success?source=touzi&jumpUrl=/user/user/myorder?type=2');
            } else {
                return $this->redirect("/info/fail?source=touzi");
            }
        }

        return $this->render('wait', ['order' => $order]);
    }

    /**
     * 查看用户合同
     * @param $asset_id
     * @return mixed
     */
    public function actionContract($asset_id)
    {
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => false]);
        if ($asset['user_id'] !== $this->getAuthedUser()->id) {
            throw new \Exception('不能查看他人的合同');
        }
        $contracts = $this->getUserContract($asset);
        $bqLoan = $contracts['bqLoan'];
        return $this->render('contract', [
            'loanContracts' => $contracts['loanContract'],
            'creditContracts' => $contracts['creditContract'],
            'bqLoan' => $bqLoan,
        ]);
    }

    /**
     * 合同页面(原始合同)
     */
    public function actionAgreement($pid, $note_id = 0)
    {
        $contracts = $this->getContractTemplate($pid, $note_id);
        return $this->render('agreement', [
            'contracts' => $contracts,
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
        } else {
            if (1 === $order->status) {
                return $this->redirect('/info/success?source=touzi&jumpUrl=/user/user/myorder?type=2');
            } else {
                return $this->redirect('/info/fail?source=touzi');
            }
        }
    }
}
