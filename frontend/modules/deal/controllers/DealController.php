<?php

namespace frontend\modules\deal\controllers;

use common\action\loan\ExpectProfitLoan;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\models\user\User;
use common\service\PayService;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class DealController extends BaseController
{
    public function actions()
    {
        return [
            'interest' => ExpectProfitLoan::className(),//服务端计算购买标的的预期收益
        ];
    }

    /**
     * 项目详情页面
     */
    public function actionDetail($sn)
    {
        $deal = $this->findOr404(OnlineProduct::className(), [
            'online_status' => OnlineProduct::STATUS_ONLINE,
            'del_status' => OnlineProduct::STATUS_USE,
            'sn' => $sn,
        ]);

        $user = $this->getAuthedUser();

        //未登录或者登录了，但不是定向用户的情况下，报404
        if ($deal->isPrivate) {
            if (null === $user) {
                throw $this->ex404('未登录用户不能查看定向标');
            } else {
                $user_ids = explode(',', $deal->allowedUids);
                if (!in_array($this->getAuthedUser()->id, $user_ids)) {
                    throw $this->ex404('不能查看他人的定向标');
                }
            }
        }

        //获取可用代金券
        $coupons = [];
        $money = 0;

        if ($deal->allowUseCoupon && $user) {
            $coupons = UserCoupon::fetchValid($user, null, $deal);
        }

        //获取session中购买数据
        $detailData = Yii::$app->session->get('detail_data', []);
        if (!empty($detailData)) {
            if (isset($detailData[$sn]['money']) && $detailData[$sn]['money'] > 0) {
                $money = $detailData[$sn]['money'];
            }
        }

        return $this->render('detail', [
            'deal' => $deal,
            'coupons' => $coupons,
            'user' => $user,
            'money' => $money,
        ]);
    }

    /**
     *获取投资记录
     */
    public function actionOrderList($pid)
    {
        $this->findOr404(OnlineProduct::className(), ['id' => $pid]);
        $ol = OnlineOrder::tableName();
        $u = User::tableName();
        $query = OnlineOrder::find()
            ->innerJoin('user',"$u.id = $ol.uid")
            ->where(["$ol.online_pid" => $pid, "$ol.status" => 1])
            ->orderBy("$ol.id desc");
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 10
        ]);
        $data = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->renderFile('@frontend/modules/deal/views/deal/_order_list.php', [
            'data' => $data,
            'pages' => $pages,
        ]);
    }

    /**
     * 检查标的
     */
    public function actionCheck($sn)
    {
        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
        $money = Yii::$app->request->post('money');
        $detailData = Yii::$app->session->get('detail_data', []);
        $userCouponIds = isset($detailData[$sn]['couponId']) ? $detailData[$sn]['couponId'] : [];
        $couponConfirm = Yii::$app->request->post('couponConfirm');
        $user = $this->getAuthedUser();

        //未登录时候保存购买数据
        if (null === $user) {
            Yii::$app->session['detail_data'][$sn] = ['money' => $money];
            return ['code' => 1, 'message' => '请登录', 'tourl' => '/site/login'];
        }

        //判断是否需要强制测评
        if (!$user->getUserIsInvested() && !$user->checkRisk()) {
            return ['code' => 1, 'message' => '需要测评', 'tourl' => '/site/risk'];
        }

        //检验代金券的使用
        $couponMoney = 0; //记录可用的代金券金额
        $couponCount = 0; //记录可用的代金券个数
        $checkMoney = $money; //校验输入的金额
        $existUnUseCoupon = false; //是否存在不可用代金券
        $lastErrMsg = ''; //最后一个不可用代金券的错误提示信息
        if (is_array($userCouponIds) && !empty($userCouponIds)) {
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
        //将所有可用的代金券写入session，并判断是否存在不可用的代金券，返回报错信息
        $detailData[$sn]['couponId'] = $userCouponIds;
        Yii::$app->session->set('detail_data', $detailData);
        if ($existUnUseCoupon) {
            return [
                'code' => 2,
                'message' => $lastErrMsg,
                'coupon' =>['count' => $couponCount, 'amount' => $couponMoney],
            ];
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($user, $sn, $money, $couponMoney, 'pc');
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }

        if ($deal->allowUseCoupon) {
            $validCoupons = UserCoupon::fetchValid($user, null, $deal);
            if (!empty($validCoupons) && '1' !== $couponConfirm) {
                return ['code' => 1, 'message' => '', 'confirm' => 1];
            }
        }

        //已登录时候保存购买数据
        $detailData[$sn]['money'] = $money;
        Yii::$app->session->set('detail_data', $detailData);

        return [
            'code' => 0,
            'message' => '',
            'tourl' => '/deal/deal/confirm?sn='.$sn.'&money='.$money,
        ];
    }

    /**
     * 确认订单页面
     */
    public function actionConfirm($sn, $money)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        $session = Yii::$app->session->get('detail_data', []);
        $userCouponIds = isset($session[$sn]['couponId']) ? $session[$sn]['couponId'] : [];
        $cou_money = 0;

        if (is_array($userCouponIds) && !empty($userCouponIds)) {
            $userCouponIds = array_filter($userCouponIds);
            $loan = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
            if (!$loan->allowUseCoupon) {
                throw new \Exception('该标的不能使用代金券');
            }

            $c = CouponType::tableName();
            $u = UserCoupon::tableName();
            $cou_money = UserCoupon::find()
                ->innerJoinWith('couponType')
                ->where(['in', "$u.id", $userCouponIds])
                ->sum("$c.amount");
        }
        $deal = $this->findOr404(OnlineProduct::className(), ['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);

        return $this->render('confirm', [
            'deal' => $deal,
            'money' => $money,
            'cou_money' => $cou_money,
            'sn' => $sn,
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