<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Request;

class GoldenEgg
{
    public $promo;

    private $orderMoneyLimit = 50000;//累计订单金额超过此金额之后发送机会

    const SOURCE_INIT = 'init';//每人一次的机会
    const SOURCE_ORDER = 'order';//购买

    //todo 定义奖品
    const AWARD_1 = 1;

    //获取活动奖品列表
    public function getAwardList()
    {
        //todo 填写奖品信息
        return [
            self::AWARD_1 => ['name' => '', 'couponSn' => ''],
        ];
    }

    //获取某个奖品信息
    public function getAward($awardId)
    {
        $awardList = $this->getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : '';
    }

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    //给用户发放机会
    public function addTicket(User $user, $ticketSource, Request $request)
    {
        $promo = $this->promo;
        if ($promo->isActive($user)) {
            switch ($ticketSource) {
                case self::SOURCE_INIT :
                    $this->initUserTicket($user, $request);
                    break;
                default:
                    break;
            }
        }
    }

    //给用户初始化机会
    private function initUserTicket(User $user, Request $request)
    {
        $ticket = PromoLotteryTicket::findOne([
            'user_id' => $user->id,
            'source' => self::SOURCE_INIT,
            'promo_id' => $this->promo->id,
        ]);
        if (empty($ticket)) {
            $ticket = new PromoLotteryTicket([
                'user_id' => $user->id,
                'source' => self::SOURCE_INIT,
                'promo_id' => $this->promo->id,
                'ip' => $request->getUserIP(),
            ]);
            $ticket->save();
        }
    }

    //订单完成之后统一调用逻辑
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promo = $this->promo;
        $user = $order->user;
        if ($promo->isActive($user)) {
            $money = OnlineOrder::find()
                ->where([
                    'user_id' => $user->id,
                    'status' => OnlineOrder::STATUS_SUCCESS,
                ])
                ->andWhere(['>', 'orderTime', $promo->startAt])
                ->sum('order_money');
            $tickets = (int)PromoLotteryTicket::find()
                ->where([
                    'user_id' => $user->id,
                    'source' => self::SOURCE_ORDER,
                    'promo_id' => $this->promo->id,
                ])
                ->count();
            $allTicket = intval($money / $this->orderMoneyLimit);
            $extraTicket = max($allTicket - $tickets, 0);
            for ($i = 1; $i <= $extraTicket; $i++) {
                $ticket = new PromoLotteryTicket([
                    'user_id' => $user->id,
                    'source' => self::SOURCE_ORDER,
                    'promo_id' => $this->promo->id,
                ]);
                $ticket->save();
            }
        }
    }
}