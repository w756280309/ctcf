<?php

namespace app\modules\order\controllers;

use app\controllers\BaseController;
use common\action\loan\ExpectProfitLoan;
use common\controllers\ContractTrait;
use common\lib\MiitBaoQuan\Miit;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\product\OnlineProduct;
use common\service\PayService;
use common\utils\StringUtils;
use Yii;
use yii\helpers\ArrayHelper;

class OrderController extends BaseController
{
    use ContractTrait;

    public function actions()
    {
        return [
            'interest' => ExpectProfitLoan::className(),//服务端计算购买标的的预期收益
        ];
    }

    /**
     * 认购页面.
     */
    public function actionIndex($sn, $rand = false)
    {
        if (!preg_match('/^[A-Za-z0-9]+$/', $sn)) {
            throw $this->ex404();
        }

        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
        $user = $this->getAuthedUser();
        $money = Yii::$app->session->getFlash('order_money');
        $coupons = [];
        $validCoupons = [];

        if ($deal->allowUseCoupon || $deal->allowRateCoupon) {  //修改T857问题
            $validCoupons = UserCoupon::fetchValid($user, null, $deal);

//            if (Yii::$app->session->has('loan_coupon')) {
//                $c = CouponType::tableName();
//                $uc = UserCoupon::tableName();
//                $session = Yii::$app->session->get('loan_coupon');
//
//                $coupons = UserCoupon::find()
//                    ->innerJoin($c, "$c.id = $uc.couponType_id")
//                    ->where(["$uc.id" => $session['couponId']])
//                    ->select('amount')
//                    ->asArray()
//                    ->all();
//            } elseif (!empty($validCoupons)) {
//                $coupon = current($validCoupons);
//                $coupons[] = ['amount' => $coupon->couponType->amount];
//
//                Yii::$app->session->set('loan_coupon', ['couponId' => [$coupon->id]]);
//            }
        }
        $res = Yii::$app->session->get('loan_coupon');


        if (is_null($res)) {
            Yii::$app->session->set('loan_coupon', ['rand' => $rand, 'money' => '', 'couponId' => '']);
        } else if (!isset($res['rand'])) {
            //Yii::$app->session->remove('loan_coupon');
            Yii::$app->session->set('loan_coupon', ['rand' => $rand, 'money' => '', 'couponId' => '']);
        }else if ($rand && $res['rand'] != $rand) {
            //Yii::$app->session->remove('loan_coupon');
            Yii::$app->session->set('loan_coupon', ['rand' => $rand, 'money' => '', 'couponId' => '']);
        }
        $res = Yii::$app->session->get('loan_coupon');
        return $this->render('index', [
            'deal' => $deal,
            'user' => $user,
            'coupons' => $coupons,
            'validCoupons' => $validCoupons,
            'money' => !is_null($res) ? $res['money'] : '',
        ]);
    }

    /**
     * 购买标的.
     */
    public function actionDoorder($sn)
    {
        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
        $money = Yii::$app->request->post('money');
        $session = Yii::$app->session->get('loan_coupon');
        $userCouponIds = isset($session['couponId']) ? $session['couponId'] : [];   //用户代金券或加息券

        $couponConfirm = Yii::$app->request->post('couponConfirm');

        $user = $this->getAuthedUser();
        /**
         * 加息券只可使用一张
         * 加息券和代金券不可同时使用
         */
        $count = count($userCouponIds);
        if ($count > 0) {
            $jiaxi_count = UserCoupon::find()
                ->innerJoinWith('couponType')
                ->where([
                    'isUsed' => false,
                    'isDisabled' => false,
                    'user_id' => $user->id,
                ])
                ->andWhere(['in', 'user_coupon.id', $userCouponIds])
                ->andWhere(['coupon_type.type' => 1])
                ->count();
            if ($jiaxi_count > 1) {
                return ['code' => '4', 'message' => '加息券每次只可使用一张'];
            } else if($jiaxi_count == 1 && $count > 1) {
                return ['code' => '4', 'message' => '加息券不可与代金券同时使用'];
            }
        }

        $pay = new PayService(PayService::REQUEST_AJAX);

        //检验代金券的使用
        $couponMoney = 0; //记录可用的代金券金额
        $couponCount = 0; //记录可用的代金券个数
        $checkMoney = $money; //校验输入的金额
        $existUnUseCoupon = false; //是否存在不可用代金券
        $lastErrMsg = ''; //最后一个不可用代金券的错误提示信息
        if (is_array($userCouponIds)) {
            $userCouponIds = array_filter($userCouponIds);
            $u = UserCoupon::tableName();
            $userCoupons = UserCoupon::find()
                ->where(['in', "$u.id", $userCouponIds])
                ->all();
            foreach ($userCoupons as $key => $userCoupon) {
                try {
                    UserCoupon::checkAllowUse($userCoupon, $checkMoney, $user, $deal);
                } catch (\Exception $ex) {
                    $lastErrMsg = $ex->getMessage();
                    $existUnUseCoupon = true;
                    unset($userCoupons[$key]);
                    continue;
                }
                $couponCount++;
                $couponType = $userCoupon->couponType;
                $couponMoney = bcadd($couponMoney, $couponType->amount, 2);
                $checkMoney = bcsub($checkMoney, $couponType->minInvest, 2);
            }
            $userCouponIds = ArrayHelper::getColumn($userCoupons, 'id');
        }

        if ($existUnUseCoupon) {
            $couponMoney = StringUtils::amountFormat2($couponMoney);
            return [
                'code' => 2,
                'message' => $lastErrMsg,
                'coupon' =>['count' => $couponCount, 'amount' => $couponMoney],
            ];
        }

        $ret = $pay->checkAllowPay($user, $sn, $money, $couponMoney);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }

        if ($deal->allowUseCoupon) {
            $validCoupons = UserCoupon::fetchValid($user, null, $deal);

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

        return $orderManager->createOrder($sn, $money, $userCouponIds, $user->id, $investFrom);
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
        $incrPoints = null;
        if (null  !== $order && 1 !== $order->status) {
            $deal = OnlineProduct::findOne($order->online_pid);
        }
        if (Yii::$app->request->isAjax) {
            return ['status' => $order->status];
        }

        //获取该订单增加了多少积分
        if (null !== $order && 1 === $order->status){
            $incrPoints = PointRecord::find()
                ->select('incr_points')
                ->where([
                    'user_id' => $order->user->id,
                    'ref_id' => $order->id])
                ->scalar();
        }

        return $this->render('error', [
            'order' => $order,
            'deal' => $deal,
            'incrPoints' => $incrPoints,
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
        //工信部保权合同
        if (Yii::$app->params['enable_miitbaoquan']) {
            $miit = new Miit();
            if (isset($asset['credit_order_id'])) {
                $miitBQ = $miit->viewHetong(
                    $asset['credit_order_id'],
                    EbaoQuan::TYPE_M_LOAN,
                    EbaoQuan::ITEM_TYPE_CREDIT_ORDER
                );
            } else {
                $miitBQ = $miit->viewHetong($asset['order_id']);
            }
        } else {
            $miitBQ = null;
        }

        return $this->render('contract', [
            'contracts' => $contracts,
            'fk' => $key,
            'content' => $contracts[$key]['content'],
            'asset_id' => $asset_id,
            'bq' => $bq,
            'miitBQ' => $miitBQ,
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
}
