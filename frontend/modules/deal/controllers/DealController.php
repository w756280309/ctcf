<?php

namespace frontend\modules\deal\controllers;

use common\action\loan\ExpectProfitLoan;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\models\user\User;
use common\service\PayService;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
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
        $userCouponId = 0;
        $formConfirm = 0;

        if ($deal->allowUseCoupon && $user) {
            $coupons = UserCoupon::fetchValid($user, null, $deal);
        }

        //获取session中购买数据
        $detail_data = Yii::$app->session['detail_'.$sn.'_data'];

        if ($detail_data['money'] > 0) {
            $money = $detail_data['money'];
        }

        if ($detail_data['coupon_id'] > 0) {
            $userCouponId = (int) $detail_data['coupon_id'];
            $formConfirm = 1;
        } elseif (!empty($coupons)) {
            $userCouponId = reset($coupons)->id;
        }

        return $this->render('detail', [
            'deal' => $deal,
            'coupons' => $coupons,
            'user' => $user,
            'money' => $money,
            'coupon_id' => $userCouponId,
            'fromConfirm' => $formConfirm,
        ]);
    }

    /**
     * 根据输入的金额自动获取代金券.
     */
    public function actionValidForLoan()
    {
        $request = array_replace([
            'sn' => null,
            'money' => null,
        ], Yii::$app->request->get());

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (empty($request['money']) || !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $request['money'] = null;
        }

        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        if ($deal->allowUseCoupon) {
            $coupons = UserCoupon::fetchValid($this->getAuthedUser(), $request['money'], $deal);

            if (empty($coupons)) {
                $backArr = [
                    'code' => 1,
                    'message' => '没有可用代金券',
                ];
            } else {
                $backArr = [
                    'code' => 0,
                    'message' => '查询成功',
                    'couponId' => reset($coupons)->id,
                ];
            }
        } else {
            $backArr = [
                'code' => 1,
                'message' => '当前标的不允许使用代金券',
            ];
        }


        return $backArr;
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
        $couponId = Yii::$app->request->post('couponId');
        $couponConfirm = Yii::$app->request->post('couponConfirm');
        $user = $this->getAuthedUser();

        $coupon = null;
        if ($deal->allowUseCoupon && $couponId) {
            $coupon = UserCoupon::findOne($couponId);
            if (null === $coupon) {
                return ['code' => 1, 'message' => '无效的代金券'];
            }
        }

        //未登录时候保存购买数据
        if (null === $user) {
            Yii::$app->session['detail_' . $sn . '_data'] = ['money' => $money];
            return ['code' => 1, 'message' => '请登录', 'tourl' => '/site/login'];
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($user, $sn, $money, $coupon, 'pc');
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
        Yii::$app->session['detail_'.$sn.'_data'] = [
            'money' => $money,
            'coupon_id' => $couponId,
        ];

        return [
            'code' => 0,
            'message' => '',
            'tourl' => '/deal/deal/confirm?sn='.$sn.'&money='.$money.'&coupon_id='.$couponId,
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

        $coupon_id = Yii::$app->request->get('coupon_id');
        $coupon = null;
        if ($coupon_id) {
            $loan = $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
            if (!$loan->allowUseCoupon) {
                throw new \Exception('该标的不能使用代金券');
            }

            $coupon = $this->findOr404(UserCoupon::className(), $coupon_id);
        }
        $deal = $this->findOr404(OnlineProduct::className(), ['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        $cou_money = $coupon ? ($coupon->couponType ? $coupon->couponType->amount : 0) : 0;

        return $this->render('confirm', [
            'deal' => $deal,
            'coupon' => $coupon,
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