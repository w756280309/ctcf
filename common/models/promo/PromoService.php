<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class PromoService
{
    /**
     * 获取对指定用户有效的活动
     * @param User|null $user
     * @return array
     */
    private static function getActivePromo(User $user = null)
    {
        $time = time();
        $promos = RankingPromo::find()->andWhere(['>=', 'endAt', $time])->andWhere('`key` is not null')->andWhere('promoClass is not null')->all();
        $data = [];
        foreach ($promos as $promo) {
            if ($promo->isActive($user)) {
                if(class_exists($promo->promoClass)) {
                   $data[] = $promo;
                }
            }
        }
        return $data;
    }

    /**
     * @param User $user            用户对象
     * @param $ticketSource string  抽奖机会来源
     */
    public static function addTicket(User $user, $ticketSource)
    {
        $promos = self::getActivePromo($user);
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addTicket')) {
                $model->addTicket($user, $ticketSource, \Yii::$app->request);
            }
        }
    }

    /**
     * 给被被邀请者送代金券
     * @param User $user
     */
    public static function addInviteeCoupon(User $user)
    {
        $promos = self::getActivePromo($user);
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addInviteeCoupon')) {
                $model->addInviteeCoupon($user);
            }
        }
    }

    /**
     * 标的订单完成之后的活动逻辑
     * @param OnlineOrder $order
     */
    public static function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterSuccessLoanOrder')) {
                $model->doAfterSuccessLoanOrder($order);
            }
        }
    }
}