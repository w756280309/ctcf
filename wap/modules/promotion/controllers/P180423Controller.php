<?php

namespace wap\modules\promotion\controllers;

use common\models\mall\PointRecord;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use Yii;
use common\models\promo\Reward;
use wap\modules\promotion\models\RankingPromo;
use yii\db\Exception;

class P180423Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    public $subtractPointValue = 200;
    /**
     * 月末积分抽奖活动初始化页面
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isLoggedIn 判断登录状态，已登录：true,未登录：false
     * isDrawn 判断是否已抽过奖，true：用户已抽过一次奖; false:用户未抽过奖
     * rewards 奖池
     * sn ： reward表的sn
     * name: 奖品名称
     * path：奖品图片路径
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180423']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $rewards = Reward::find()
            ->select('sn, name')
            ->where(['promo_id' => $promo->id])
            ->all();

        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
            'isDrawn' => false,
            'rewards' => $rewards,
        ];

        if (null !== $user) {
            $promoClass = new $promo->promoClass($promo);
            $data['isDrawn'] = null !== $promoClass->isDrawnTicket($user);
        }
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 抽奖接口
     *
     * 提交方式：AJAX get
     * 地址：/promotion/p180423/get-draw
     * 输入参数:key=promo_180423
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,8,9
     *          'message' => '成功，未开始，已结束，未登录，积分不足，系统繁忙',
     *          'ticket' => $record->sn,  //奖品的sn，与奖品初始化方法中奖品列表对应
     *          'points' => $user->points 用户的剩余积分
     *      ]
     *
     * @return array
     */
    public function actionGetDraw()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180423']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            PointRecord::subtractUserPoints($user, $this->subtractPointValue);
            PromoLotteryTicket::initNew($user, $promo, 'points')->save(false);
            $ticket = PromoService::draw($promo, $user);
            Yii::$app->response->statusCode = 200;
            return [
                'code' => 0,
                'message' => '成功',
                'ticket' => $ticket->reward->sn,
            ];
        } catch (\Exception $e) {
            $code = $e->getCode();
            return $this->getErrorByCode($code);
        }
    }



}