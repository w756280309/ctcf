<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\Reward;
use common\utils\StringUtils;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180115Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 活动落地页
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180115']);
        $rankingList = [];  //排行榜
        $rewardList = [];   //奖品列表-用于控制大转盘
        $registerMobileList = [];   //第三个获奖列表 - 19日下午17:00公布
        $activeTicketCount = 0;     //剩余抽奖次数
        $currentMobile = '';

        //排行榜
        $redis = Yii::$app->redis;
        if ($redis->exists('ranking-list')) {
            $rankingList = json_decode($redis->get('ranking-list'), true);
        }
        foreach ($rankingList as $k => $ranking) {
            $rankingList[$k]['subMobile'] = StringUtils::obfsLandlineNumber($ranking['mobile']);
        }

        //奖品列表
        $rewardList = Reward::find()
            ->select('name')
            ->where(['promo_id' => $promo->id])
            ->indexBy('sn')
            ->column();

        //剩余抽奖次数
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $currentMobile = $user->getMobile();
            $promoClass = new $promo->promoClass($promo);
            $activeTicketCount = $promoClass->getActiveTicketCount($user);
        }

        //获得第三模块注册手机号接近开证指数中奖的手机号
        $promoRegister = RankingPromo::findOne(['key' => 'promo_180119']);
        $registerPromoClass = new $promoRegister->promoClass($promoRegister);
        $registerMobileList = $registerPromoClass->getAwardMobileList();
        $this->registerPromoStatusInView($promo);

        return $this->render('index', [
            'rankingList' => $rankingList,
            'rewardList' => $rewardList,
            'activeTicketCount' => $activeTicketCount,
            'registerMobileList' => $registerMobileList,
            'currentMobile' => $currentMobile,
        ]);
    }
}
