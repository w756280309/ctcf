<?php

namespace app\modules\order\controllers;

use app\controllers\BaseController;
use common\controllers\ContractTrait;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\service\PayService;
use Yii;
use yii\helpers\Html;

class OrderController extends BaseController
{
    use ContractTrait;

    /**
     * 认购页面.
     */
    public function actionIndex()
    {
        $request = array_replace([
            'sn' => null,
            'money' => null,
            'userCouponId' => null,
        ], Yii::$app->request->get());

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (empty($request['money']) || !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $request['money'] = null;
        }

        if (empty($request['userCouponId']) || !preg_match('/^[0-9]+$/', $request['userCouponId'])) {
            $request['userCouponId'] = null;
        }

        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);
        $user = $this->getAuthedUser();
        $coupons = [];

        if ($deal->allowUseCoupon) {
            $coupons = $this->userCoupon($user);
        }

        return $this->render('index', [
            'deal' => $deal,
            'user' => $user,
            'coupons' => $coupons,
            'userCouponId' => $request['userCouponId'],
            'money' => $request['money'],
        ]);
    }

    /**
     * 获取用户可用的代金券.
     */
    private function userCoupon($user)
    {
        return UserCoupon::validList($user)
            ->indexBy('id')
            ->orderBy([
                'expiryDate' => SORT_ASC,
                'amount' => SORT_DESC,
                'minInvest' => SORT_ASC,
                'id' => SORT_DESC,
            ])->all();
    }

    /**
     * 购买标的.
     */
    public function actionDoorder($sn)
    {
        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);

        $money = Yii::$app->request->post('money');
        $userCouponId = Yii::$app->request->post('couponId');
        $couponConfirm = Yii::$app->request->post('couponConfirm');

        $user = $this->getAuthedUser();

        $coupon = null;
        if ($deal->allowUseCoupon && !empty($userCouponId)) {
            $coupon = UserCoupon::findOne($userCouponId);
            if (null === $coupon) {
                return ['code' => 1,  'message' => '无效的代金券'];
            }
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($user, $sn, $money, $coupon);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }

        if ($deal->allowUseCoupon) {
            $validCoupons = $this->userCoupon($user);

            if (!empty($validCoupons) && '1' !== $couponConfirm) {
                return ['code' => 1, 'message' => '', 'confirm' => 1];
            }
        }

        $orderManager = new OrderManager();
        //记录订单来源
        $investFrom = OnlineOrder::INVEST_FROM_WAP;

        if (defined('IN_APP') && IN_APP) {
            $investFrom = OnlineOrder::INVEST_FROM_APP;
        }

        if ($this->fromWx()) {
            $investFrom  = OnlineOrder::INVEST_FROM_WX;
        }

        return $orderManager->createOrder($sn, $money,  $user->id, $coupon, $investFrom);
    }

    /**
     * 认购标的结果页
     */
    public function actionResult($osn)
    {
        if (empty($osn)) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);
        $deal = null;
        if (null  !== $order && 1 !== $order->status) {
            $deal = OnlineProduct::findOne($order->online_pid);
        }
        if (Yii::$app->request->isAjax) {
            return ['status' => $order->status];
        }

        return $this->render('error', [
            'order' => $order,
            'deal' => $deal,
            'ret' => (null  !== $order && 1 === $order->status) ? 'success' : 'fail',
        ]);
    }

    /**
     * 认购标的中间处理页
     */
    public function actionWait($osn)
    {
        if (empty($osn)) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);

        return $this->render('wait', [
            'order' => $order,
        ]);
    }

    /**
     * 查看用户合同.
     */
    public function actionContract($asset_id, $key = 0)
    {
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => false]);
        if ($asset['user_id'] !== $this->getAuthedUser()->id) {
            throw new \Exception('不能查看他人的合同');
        }
        $contracts = $this->getUserContract($asset);
        $bqLoan = $contracts['bqLoan'];
        $contracts = array_merge($contracts['loanContract'], $contracts['creditContract']);
        $key = isset($contracts[$key]) ? $key : 0;
        if ($contracts[$key]['type'] === 'loan' && !empty($bqLoan)) {
            $bq = $bqLoan;
        } elseif (in_array($contracts[$key]['type'], ['credit_note', 'credit_order']) && !empty($contracts[$key]['bqCredit'])) {
            $bq = $contracts[$key]['bqCredit'];
        } else {
            $bq = [];
        }

        $isDisDownload = defined('IN_APP') || ($this->fromWx() && !($_SERVER['HTTP_USER_AGENT'] && strpos($_SERVER['HTTP_USER_AGENT'], 'Android')));

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@wap/modules/order/views/order/_contract.php', ['content' => $contracts[$key]['content'], 'bq' => $bq, 'isDisDownload' => $isDisDownload]);
        }

        return $this->render('contract', [
            'contracts' => $contracts,
            'fk' => $key,
            'content' => $contracts[$key]['content'],
            'asset_id' => $asset_id,
            'bq' => $bq,
            'isDisDownload' => $isDisDownload,  //APP端,以及非安卓微信端打开此页面,不允许下载
        ]);
    }

    /**
     * 合同显示页面.
     */
    public function actionAgreement($id, $note_id = 0, $key = 0)
    {
        $contracts = $this->getContractTemplate($id, $note_id);
        $key = isset($contracts[$key]) ? $key : 0;
        $content = $contracts[$key]['content'];

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@wap/modules/order/views/order/_contract.php', ['content' => $content]);
        }

        return $this->render('contract', [
            'contracts' => $contracts,
            'content' => $content,
            'fk' => $key,
            'note_id' => $note_id,
            'id' => $id,
        ]);
    }

    /**
     * 根据投资金额和产品利率阶梯获取订单的利率
     * @return array
     */
    public function actionRate()
    {
        if (Yii::$app->request->isPost) {
            $sn = Html::encode(Yii::$app->request->post('sn'));
            $amount = Html::encode(Yii::$app->request->post('amount'));
            $product = OnlineProduct::find()->where(['sn' => $sn])->one();
            if ($product && $amount) {
                if (1 === $product->isFlexRate && !empty($product->rateSteps)) {
                    $config = RateSteps::parse($product->rateSteps);
                    if (!empty($config)) {
                        $rate = RateSteps::getRateForAmount($config, $amount);
                        if (false !== $rate) {
                            return ['res' => true, 'rate' => $rate / 100];
                        }
                    }
                }
            }
            return ['res' => false, 'rate' => false];
        }
        return ['res' => false, 'rate' => false];
    }
}
