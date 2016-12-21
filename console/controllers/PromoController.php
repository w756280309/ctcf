<?php

namespace console\controllers;


use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;

/**
 * 活动定时任务类
 */
class PromoController extends Controller
{
    /**
     * 仅用于 生日送代金券活动
     * 在用户生日当天给用户发代金券
     * 每天8:50执行
     */
    public function actionSendCoupon()
    {
        $promoKey = 'promo_birthday_coupon';
        $promo = RankingPromo::findOne(['key' => $promoKey]);
        if ($promo && class_exists($promo->promoClass)) {
            $model = new $promo->promoClass($promo);
            $userList = $model->getAwardUserList();
            $model->sendAwardToUsers($userList);
        }
    }
}