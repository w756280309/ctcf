<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class PromoService
{
    /**
     * 获取对指定用户有效的活动[查找所有活动，活动是否有效在具体调用方法中自行判断]
     * @return array
     * @throws \Exception
     */
    private static function getActivePromo()
    {
        $promos = RankingPromo::find()->where('`key` is not null')->andWhere('promoClass is not null')->all();
        $data = [];
        foreach ($promos as $promo) {
            if (class_exists($promo->promoClass)) {
                $data[] = $promo;
            }
        }
        return $data;
    }

    /**
     * @param User $user 用户对象
     * @param $ticketSource string  抽奖机会来源
     * @throws \Exception
     */
    public static function addTicket(User $user, $ticketSource)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addTicket')) {
                try {
                    $model->addTicket($user, $ticketSource, \Yii::$app->request);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 给被被邀请者送代金券
     * @param User $user
     * @throws \Exception
     */
    public static function addInviteeCoupon(User $user)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addInviteeCoupon')) {
                try {
                    $model->addInviteeCoupon($user);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 标的订单完成之后的活动逻辑
     * @param OnlineOrder $order
     * @throws \Exception
     */
    public static function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterSuccessLoanOrder')) {
                try {
                    $model->doAfterSuccessLoanOrder($order);
                } catch (\Exception $ex) {

                }
            }
        }
    }
}