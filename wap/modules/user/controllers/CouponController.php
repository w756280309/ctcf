<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\action\user\AddCouponAction;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class CouponController extends BaseController
{
    public function actions()
    {
        return [
            'add-coupon' => AddCouponAction::className(), //添加或取消勾选的代金券
        ];
    }

    /**
     * 我的代金券.
     */
    public function actionList($page = 1)
    {
        $c = CouponType::tableName();
        $uc = UserCoupon::tableName();

        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => '10']);
        $model = $query
            ->select("$c.bonusDays, $c.loanCategories, $c.bonusRate, $c.type, $c.loanExpires, $c.amount, $c.name, $c.minInvest, if($uc.isUsed, bin(0),
             $uc.expiryDate < date(now())) as isExpired, $uc.expiryDate, $uc.isUsed, $uc.couponType_id")
            ->offset($pages->offset)
//            ->limit($pages->limit)
            ->orderBy("isExpired, isUsed, $uc.expiryDate, amount desc, minInvest")
            ->asArray()
            ->all();

        foreach ($model as $key => $val) {
            $model[$key]['minInvestDesc'] = StringUtils::amountFormat1('{amount}{unit}', $val['minInvest']);
        }

        $tp = $pages->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        if (\Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return [
                'header' => $pages,
                'data' => $model,
                'code' => $code,
                'message' => $message,
                'tp' => $tp,
                'cp' => $page,
            ];
        }
        //var_dump($model);die;
        return $this->render('list', ['model' => $model, 'header' => $pages]);
    }

    /**
     * 可用代金券列表.
     */
    public function actionValid($page = 1, $size = 10)
    {
        $request = $this->validateParams(Yii::$app->request->get());
        $loan = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $validCoupons = UserCoupon::fetchValid($this->getAuthedUser(), null, $loan);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $validCoupons,
            'pagination' => [
                'pageSize' => $size,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $dataProvider->totalCount, 'pageSize' => $size]);
        $coupons = $dataProvider->models;

        $tp = $pages->pageCount;
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        $header = [
            'count' => $pages->totalCount,
            'size' => $pages->pageSize,
            'tp' => $tp,
            'cp' => $pages->page + 1,
        ];

        $selectedCoupon = [];
        $key = 'loan_coupon';
        $couponCount = 0;
        $couponMoney = 0;

        if (Yii::$app->session->has($key)) {
            $session = Yii::$app->session->get($key);
            $selectedCoupon = $session['couponId'];

            foreach ($selectedCoupon as $couponId) {
                $coupon = UserCoupon::findOne($couponId);
                if (is_null($coupon) || is_null($coupon->couponType)) {
                    continue;
                }
                $couponCount++;
                $couponMoney = bcadd($couponMoney, $coupon->couponType->amount, 2);
            }
        }

        $backArr = [
            'coupons' => $coupons,
            'sn' => $request['sn'],
            'money' => $request['money'],
            'selectedCoupon' => $selectedCoupon,
        ];

        Yii::$app->session->setFlash('order_money', $request['money']);

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_valid_list', $backArr);

            return [
                'header' => $header,
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        $this->layout = '@app/views/layouts/fe';

        return $this->render('valid_list', array_merge($backArr, [
            'header' => $header,
            'couponCount' => $couponCount,
            'couponMoney' => $couponMoney,
            'loan' => $loan,
        ]));
    }

    /**
     * 根据输入的金额自动获取代金券.
     */
    public function actionValidForLoan()
    {
        $coupon = null;
        $request = $this->validateParams(Yii::$app->request->get());
        $loan = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $coupons = UserCoupon::fetchValid($this->getAuthedUser(), $request['money'], $loan);
        $userCouponId = [];

        if ($coupons) {
            $coupon = reset($coupons);
            $userCouponId[] = $coupon->id;
        }

        Yii::$app->session->set('loan_coupon', ['couponId' => $userCouponId]);

        $this->layout = false;

        return $this->render('_valid_coupon', ['coupons' => $coupon ? [['amount' => $coupon->couponType->amount]] : []]);
    }

    /**
     * 清空代金券操作.
     */
    public function actionDelCoupon($sn)
    {
        Yii::$app->session->remove('loan_coupon');
        if (is_null(Yii::$app->session->get('loan_coupon'))) {
            return 'success';
        } else {
            return 'error';
        }
//        var_dump(Yii::$app->session->get('loan_coupon'));
        if ($this->validateLoanWithCoupon($sn, 0)) {
            //Yii::$app->session->set('loan_coupon', ['couponId' => []]);
            Yii::$app->session->remove('loan_coupon');
        }
    }

    private function validateLoanWithCoupon($sn, $couponId)
    {
        if (empty($sn) || !preg_match('/^[A-Za-z0-9]+$/', $sn)) {
           return false;
        }

        if (!empty($couponId) && !preg_match('/^[0-9]+$/', $couponId)) {
            return false;
        }

        return OnlineProduct::findOne(['sn' => $sn]);
    }

    private function validateParams($get)
    {
        $request = array_replace([
            'sn' => null,
            'money' => null,
        ], $get);

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (empty($request['money']) || !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $request['money'] = null;
        }

        return $request;
    }

    /**
     * 获取用户可用的代金券和加息券
     * ajax方法请求
     * @author ZouJianShuang
     */
    public function actionAvailableCoupons($money)
    {
        if (true || Yii::$app->request->isAjax) {
            $res = $res = Yii::$app->session->get('loan_coupon');
            if ($money != $res['money']) {
                Yii::$app->session->remove('loan_coupon');
                Yii::$app->session->set('loan_coupon', ['rand' => $res['rand'], 'money' => '', 'couponId' => '']);
            }
            if ($money) {
                $res = UserCoupon::availableCoupons($money);
            } else {
                $res = UserCoupon::availableCoupons();
            }

            return $res;
        }
    }
    /**
     * 每次点击优惠券前，判断单个是否可用
     */
    public function actionValidateCoupon($sn, $couponId, $money)
    {
        $coupon = UserCoupon::findOne($couponId);
        $loan = OnlineProduct::findOne(['sn' => $sn]);
        $user = Yii::$app->user->getIdentity();
        try {
            UserCoupon::checkAllowUse($coupon, $money, $user, $loan);
        } catch (\Exception $ex) {
            return ['code' => 0, 'message' => $ex->getMessage()];
        }

        return ['code' => 1, 'message' => 'ok'];

    }
}
