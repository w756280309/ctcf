<?php

namespace wap\modules\promotion\controllers;

use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;

class P171001Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171001']);
        $user = $this->getAuthedUser();

        //活动状态
        $promoStatus = $this->getPromoStatus($promo);

        //累计年化金额
        $totalMoney = 0;
        $startTime = new \DateTime($promo->startTime);
        $endTime = new \DateTime($promo->endTime);
        if (null !== $user) {
            $totalMoney = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
        }

        //写入layout文件
        $view = \Yii::$app->view;
        $view->params['promoStatus'] = $promoStatus;

        return $this->render('index', [
            'totalMoney' => rtrim(rtrim(bcdiv($totalMoney, 10000, 2), '0'), '.'),
        ]);
    }
}
