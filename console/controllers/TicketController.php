<?php

namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\promo\PromoEgg;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;

class TicketController extends Controller
{
    private $orderMoneyLimit = 30000; //累计投资金额

    /**
     * php yii ticket/supplement
     *
     * 补充限时砸金蛋活动从活动时间开始到现在的累计投资金额的lottery_tickets
     *
     * @return int
     */
    public function actionSupplement()
    {
        $ticketsCount = 0;
        $peopleCount = 0;
        $promo = RankingPromo::findOne(['key' => 'promo_070410_egg']);
        $orders = OnlineOrder::find()
            ->select(['uid', 'sum(order_money) as total'])
            ->where([
                'status' => OnlineOrder::STATUS_SUCCESS,
            ])
            ->andWhere(['>=', 'order_time', strtotime($promo->startTime)])
            ->groupBy('uid')
            ->asArray()
            ->all();

        foreach ($orders as $order) {
            $tickets = (int) PromoLotteryTicket::find()
                ->where([
                    'user_id' => $order['uid'],
                    'source' => PromoEgg::SOURCE_ORDER,
                    'promo_id' => $promo->id,
                ])
                ->count();
            $allTicket = intval($order['total'] / $this->orderMoneyLimit);
            $extraTicket = max($allTicket - $tickets, 0);
            if ($extraTicket < 1) {
                continue;
            }
            $peopleCount++;
            for ($i = 1; $i <= $extraTicket; $i++) {
                $ticket = new PromoLotteryTicket([
                    'user_id' => $order['uid'],
                    'source' => PromoEgg::SOURCE_ORDER,
                    'promo_id' => $promo->id,
                ]);
                $ticket->save();
                $ticketsCount++;
            }
        }
        echo '补充人数为' . $peopleCount . '，补充tickets个数为' . $ticketsCount;

        return self::EXIT_CODE_NORMAL;
    }
}
