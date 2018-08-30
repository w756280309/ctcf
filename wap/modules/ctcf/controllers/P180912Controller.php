<?php

namespace wap\modules\ctcf\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180912Controller extends BaseController
{
    //初始化页面接口
    public function actionIndex()
    {
        $key = \Yii::$app->request->get('key');
        $promo = $this->findOr404(RankingPromo::className(), ['key' => $key]);
        $user = $this->getAuthedUser();
        $userAnnualInvest = 0;
        $hasReward = 0;
        $isLoggedIn = null !== $user;
        if ($isLoggedIn) {
            $promoClass = new $promo->promoClass($promo);
            $userAnnualInvest = $promoClass->calcUserAmount($user, true, true);
            $hasReward = $promoClass->getCashAmount($user);
        }

        return [
            'isLoggedIn' => $isLoggedIn,
            'promoStatus' => $this->getPromoStatus($promo),
            'userAnnualInvest' => $userAnnualInvest,
            'hasReward' => $hasReward,
            'client' => \Yii::$app->params['clientOption']['host']['wap'],
        ];
    }
}
