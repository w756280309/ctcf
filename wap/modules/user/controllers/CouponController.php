<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;

class CouponController extends BaseController
{
    /**
     * 我的代金券
     */
    public function actionList()
    {
        $c = CouponType::tableName();

        $model = UserCoupon::find()
            ->innerJoin($c, "couponType_id = $c.id")
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0])
            ->all();

        return $this->render('list', ['model' => $model]);
    }
}