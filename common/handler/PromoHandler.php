<?php

namespace common\handler;

use wap\modules\promotion\models\RankingPromo;

class PromoHandler
{
    /**
     * 订单成功handler
     * @param $event
     * @return bool
     */
    public static function onOrderSuccess($event)
    {
        $order = $event->order;
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterOrderSuccess')) {
                try {
                    $model->doAfterOrderSuccess($order);
                } catch (\Exception $ex) {
                    //todo 保证注册流程暂不做处理，后续需要优化
                }
            }
        }

        return true;
    }

    //获取存在model的活动
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
}
