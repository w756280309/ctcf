<?php

namespace wap\modules\ctcf\controllers;

use common\models\promo\BasePromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use wap\modules\promotion\models\RankingPromo;

class P180513Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     *  母亲节活动,初始化页面，判断年化投资额是否可以发奖，并发送奖励，活动期间单个奖品每人获取1次
     *  接口地址：/ctcf/p180513/index
     *  返回字段：
     *  promoStatus: 1,活动未开始  2,活动已结束
     *  isLoggedIn : true,已登录  false,未登录
     *  rewards: 是否获取该奖品
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180513']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $annualInvest = 0;
        $rewards = $this->getRewards($annualInvest);
        if ($user) {
            $promoClass = new $promo->promoClass($promo);
            $annualInvest = $promoClass->calcUserAmount($user, true);
            $annualInvest /= 10000;
            $rewards = $this->getRewards($annualInvest);
            foreach ($rewards as $key => $value) {
                if ($value) {
                    $hasTicket = $promoClass->fetchOneActiveTicket($user, $key);
                    if (!$hasTicket) {
                        PromoLotteryTicket::initNew($user, $promo, $key)->save(false);
                        $reward = Reward::fetchOneBySn($key);
                        $ticket = $promoClass->fetchOneActiveTicket($user, $key);
                        PromoService::award($user, $reward, $promo, $ticket);
                    }
                }
            }
        }
        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
            'rewards' => $rewards,
        ];

        $this->renderJsInView($data);

        return $this->render('index', [
            'annualInvestAmount' => $annualInvest,
        ]);
    }
    //根据年化投资金额判断该奖品是否可以发送奖励
    private function getRewards($annualAmount)
    {
        return [
            '180513_P66' => $annualAmount > 0  ? true : false,                //66积分,   true为已发奖，false为未发奖
            '180513_C66' => $annualAmount >= 1 ? true : false,               //6.6元现金红包
            '180513_C50' => $annualAmount >= 5 ? true : false,               //50元代金券
            '180513_G50' => $annualAmount >= 10 ? true : false,              //50元超市卡
            '180513_G100' => $annualAmount >= 20 ? true : false,              //100元超市卡
            '180513_XYJ' => $annualAmount >= 50 ? true : false,                               //欧姆龙电子血压计
        ];
    }
}