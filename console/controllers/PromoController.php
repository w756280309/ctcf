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
            //活动上线之后需要先判断活动时间，不然当活动结束之后，仍然会查找所有当天生日的用户
            if ($promo->isOnline) {
                $date = date('Y-m-d');
                if ($date < $promo->startTime) {
                    return false;
                }
                if (!empty($promo->endTime) && $date > $promo->endTime) {
                    return false;
                }
            }
            $model = new $promo->promoClass($promo);
            $userList = $model->getAwardUserList();
            $model->sendAwardToUsers($userList);
        }
    }
}