<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class AwardListController extends Controller
{
    public function actionIndex()
    {
        $key = Yii::$app->request->get('key');
        $user = Yii::$app->user->getIdentity();
        if (empty($key) || null === ($promo = RankingPromo::findOne(['key' => $key])) || null === $user) {
            return [];
        }

        if (!empty($promo->promoClass)) {
            $promoClass = new $promo->promoClass($promo);
            if (method_exists($promoClass, 'getAwardList')) {
                return $promoClass->getAwardList($user);
            }
        }

        return [];
    }

}
