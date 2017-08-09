<?php

namespace wap\modules\promotion\controllers;

use common\models\order\OnlineOrder;
use common\models\promo\Draw1708;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;

class Draw1708Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $key = 'promo_170810';
        $promo = $this->findOr404(RankingPromo::class, ['key' => $key]);
        $restCount = 0;
        $promoStatus = 0;
        $todayAnnualInvest = 0;
        $invitePeopleCount = 0;
        $rewardList = [];
        $promoClass = new Draw1708($promo);
        $user = $this->getAuthedUser();
        $taskStatus = [
            'checkInFinished' => false,
            'investFinished' => false,
            'inviteFinished' => false,
        ];

        //活动状态
        try {
            $promo->isActive($user);
        } catch (\Exception $ex) {
            $promoStatus = $ex->getCode();
        }

        if (null !== $user) {
            $rewardList = $promoClass->getDrawnList($user);
            $restCount = $promoClass->getActiveTicketCount($user);
            $todayDateTime = new \DateTime();
            $todayDate = $todayDateTime->format('Y-m-d');
            $todayAnnualInvest = UserInfo::calcAnnualInvest($user->id, $todayDate, $todayDate);
            $invitePeopleCount = $promoClass->getInvestedInviteeCount($user, $todayDateTime);
            $taskStatus = $promoClass->getTasktSatus($user, $todayDateTime);
        }

        return $this->render('index', [
            'promo' => $promo,
            'user' => $user,
            'restCount' => $restCount,
            'rewardList' => $rewardList,
            'promoStatus' => $promoStatus,
            'todayAnnualInvest' => $todayAnnualInvest,
            'invitePeopleCount' => $invitePeopleCount,
            'taskStatus' => $taskStatus,
        ]);
    }
}