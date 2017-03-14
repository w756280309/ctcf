<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class CouponController extends BaseController
{
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
            ->select("$c.amount, $c.name, $c.minInvest, if($uc.isUsed, bin(0), $uc.expiryDate < date(now())) as isExpired, $uc.expiryDate, $uc.isUsed, $uc.couponType_id")
            ->offset($pages->offset)
            ->limit($pages->limit)
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

            return ['header' => $pages, 'data' => $model, 'code' => $code, 'message' => $message, 'tp' => $tp, 'cp' => $page];
        }

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

        $backArr = [
            'coupons' => $coupons,
            'sn' => $request['sn'],
            'money' => $request['money'],
        ];

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

        return $this->render('valid_list', array_merge($backArr, ['header' => $header]));
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
        if ($coupons) {
            $coupon = reset($coupons);
            $this->actionAddCouponSession($request['sn'], $coupon->id);
        }

        $this->layout = false;

        return $this->render('_valid_coupon', ['coupon' => $coupon]);
    }

    /**
     * 将对应的代金券ID存入session当中.
     */
    public function actionAddCouponSession($sn, $couponId)
    {
        if ($this->validateLoanWithCoupon($sn, $couponId)) {
            Yii::$app->session->set('loan_'.$sn.'_coupon', ['couponId' => $couponId]);
        }
    }

    /**
     * 清空代金券操作.
     */
    public function actionDelCoupon($sn)
    {
        if ($this->validateLoanWithCoupon($sn, 0)) {
            Yii::$app->session->set('loan_'.$sn.'_coupon', ['couponId' => 0]);
        }
    }

    private function validateLoanWithCoupon($sn, $couponId)
    {
        if (empty($sn) || !preg_match('/^[A-Za-z0-9]+$/', $sn)) {
            throw $this->ex404();
        }

        if (!preg_match('/^[0-9]+$/', $couponId)) {
            throw $this->ex404();
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
}