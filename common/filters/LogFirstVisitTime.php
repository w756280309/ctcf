<?php

namespace common\filters;

use common\models\promo\PromoPoker;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\base\ActionFilter;

class LogFirstVisitTime extends ActionFilter
{
    public function beforeAction($action)
    {
        //判断当前请求是Ajax，返回true
        if (Yii::$app->request->isAjax) {
            return true;
        }

        $user = Yii::$app->user->identity;
        //判断当前用户是否登录，返回true
        if (null === $user) {
            return true;
        }
        //判断活动
        $promo = RankingPromo::find()
            ->where(['key' => 'promo_poker'])
            ->one();
        if (null === $promo) {
            return true;
        }

        //发红桃号码牌（每周首次登录状态下访问网站页面）
        try {
            $promoPoker = new PromoPoker($promo);
            $promoPoker->deal($user, [
                'poker_type' => 'heart',
                'issueTime' => (new \DateTime()),
                'order_id' => null,
            ]);
        } catch (\Exception $ex) {
            //防止重复插入时报错
        }

        return true;
    }
}
