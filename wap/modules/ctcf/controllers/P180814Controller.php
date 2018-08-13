<?php

namespace wap\modules\ctcf\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180814Controller extends BaseController
{
    //初始化页面接口
    public function actionIndex()
    {
        $key = \Yii::$app->request->get('key');
        $promo = $this->findOr404(RankingPromo::className(), ['key' => $key]);
        $promoClass = new $promo->promoClass($promo);
        $user = $this->getAuthedUser();
        $code = 0;
        $message = '成功';
        $isLoggedIn = false;
        $steps = 0;
        $unRead = 0;
        $firstPop = false;
        $award = [];
        if (null !== $user) {
            $isLoggedIn = true;
            try {
                $this->checkStatus($promo, $user);
                $result = $promoClass->awardUserBySteps($user);
                $steps = $promoClass->getRecordQuery($user)->sum('quantity');
                $unRead = $promoClass->getRecordQuery($user, null, null, false)->sum('quantity');
                $award = $result['awards'];
                $firstPop = $result['addResult']['addFree'];
            } catch (\Exception $e) {
                $code = $e->getCode();
                if (2 == $code) {
                    $steps = $promoClass->getRecordQuery($user)->sum('quantity');
                }
                $message = $this->getErrorByCode($e->getCode());
                \Yii::$app->response->statusCode = 200;
                \Yii::info($e, 'promo_log');
            }
        }

        return [
            'code' => $code,
            'message' => $message,
            'data' => [
                'promoStatus' => $this->getPromoStatus($promo),
                'isLoggedIn' => $isLoggedIn,
                'steps' => $steps,
                'unRead' => $unRead,
                'firstPop' => $firstPop,
                'award' => $award,
                'appId' => \Yii::$app->params['weixin']['appId'],
                'client' => \Yii::$app->params['clientOption']['host']['wap'],
            ],
        ];
    }

    //步数列表接口
    public function actionStepList()
    {
        $key = \Yii::$app->request->get('key');
        $promo = $this->findOr404(RankingPromo::className(), ['key' => $key]);
        $promoClass = new $promo->promoClass($promo);
        $user = $this->getAuthedUser();
        $stepRecord = [];
        if (null !== $user) {
            $stepRecord = $promoClass->dealRecord($user);
        }

        return [
            'code' => 0,
            'message' => '成功',
            'data' => $stepRecord,
        ];
    }
}
