<?php

namespace common\models\promo;

use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use common\models\order\OnlineOrder;
use common\models\user\User;

class Promo201705
{
    public $promo;
    public $promoConfig;

    #抽奖机会 === 勋章
    const SOURCE_INIT = 'init'; //任意金额（不含转让和新手标）
    const SOURCE_ORDER = 'order'; //累计投资
    const SOURCE_INVITE = 'invite'; //邀请

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        $this->promoConfig = json_decode($promo->config, true);
    }

    /**
     * 根据promo中config配置的各个分活动时间，获得抽奖机会来源
     */
    public static function getTicketSource($activePromo)
    {
        return isset($activePromo['ticketSource']) ? $activePromo['ticketSource'] : '';
    }

    /**
     * 根据key获得对应的分活动配置数组
     *
     * @param string $key       三种类型 与常量对应
     * @param string $orderTime 时间
     *
     * @return array
     */
    public function getActivePromo($key, $orderTime)
    {
        foreach ($this->promoConfig[$key] as $promo) {
            if ($promo['startTime'] <= $orderTime && $promo['endTime'] >= $orderTime) {
                return $promo;
            }
        }

        return [];
    }

    /**
     * 获得一个待保存的ticket
     *
     * @param User $user
     * @param $ticketSource
     *
     * @return PromoLotteryTicket
     */
    public function initTicket(User $user, $ticketSource)
    {
        $ticket = new PromoLotteryTicket([
            'user_id' => $user->id,
            'source' => $ticketSource,
            'promo_id' => $this->promo->id,
        ]);

        return $ticket;
    }

    /**
     * 获得奖池数组
     *
     * reward表可以添加中奖概率字段
     *
     * return array
     */
    public function getPool()
    {
        return [];
    }

    /**
     * 抽奖
     *
     * @param User $user
     *
     * @return PromoLotteryTicket
     * @throws \Exception
     */
    public function draw(User $user)
    {
        return new PromoLotteryTicket();
    }

    /**
     * 订单完成之后统一调用逻辑
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        $loan = $order->loan;
        $kinds = [self::SOURCE_INIT, self::SOURCE_ORDER];
        foreach ($kinds as $source) {
            //如果是新手标且在查找任意金额投资的抽奖机会，不能添加抽奖机会
            if ($loan->is_xs && $source === self::SOURCE_INIT) {
                continue;
            }
            $extraPromo = $this->getActivePromo($source, date('Y-m-d H:i:s', $order->order_time));
            if ($extraPromo) {
                $source = self::getTicketSource($extraPromo);
                if ('' === $source) {
                    throw new \Exception('未获得正确的ticket来源');
                }
                $ticketLimit = (int) $extraPromo['limit'];
                $moneyLimit = (float) $extraPromo['moneyLimit'];
                $ticketCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
                    ->andWhere(['source' => $source])
                    ->andWhere(['user_id' => $user->id])
                    ->count();
                if ($ticketCount < $ticketLimit) {
                    $annualInvest = UserInfo::calcAnnualInvest($user->id, date('Y-m-d', strtotime($extraPromo['startTime'])), date('Y-m-d', strtotime($extraPromo['endTime'])));
                    if ($annualInvest >= $moneyLimit) {
                        $this->initTicket($user, $source)->save();
                    }
                }
            }
        }
    }

    /**
     * 绑卡成功后调用
     *
     * 给邀请人添加抽奖机会（邀请一个已经绑卡成功的用户）
     * 最多为3个
     */
    public function doAfterBindCard(User $user)
    {
        if ($user->isInvited($this->promo->startTime, $this->promo->endTime)) {
            $extraPromo = $this->getActivePromo(self::SOURCE_INVITE, date('Y-m-d H:i:s', $user->created_at));
            if ($extraPromo) {
                $source = self::getTicketSource($extraPromo);
                if ('' === $source) {
                    throw new \Exception('未获得正确的ticket来源');
                }
                $ticketLimit = (int) $extraPromo['limit'];
                $record = InviteRecord::find()
                    ->where(['invitee_id' => $user->id])
                    ->one();
                if (null !== $record) {
                    $user = User::findOne($record->user_id);
                    if (null !== $user) {
                        $ticketCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
                            ->andWhere(['source' => $source])
                            ->andWhere(['user_id' => $user->id])
                            ->count();
                        if ($ticketCount < $ticketLimit) {
                            $this->initTicket($user, $source)->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getRestTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => false, 'user_id' => $user->id])->count();
    }

    /**
     * 获取活动奖品列表(后台活动中奖记录)
     *
     * @return array
     */
    public static function getAwardList()
    {
        $list = [];
        $promo = RankingPromo::findOne(['key' => 'promo_201705']);
        if (null !== $promo) {
            $list = Reward::find()
                ->where(['promo_id' => $promo->id])
                ->indexBy('id')
                ->asArray()
                ->all();
        }

        return $list;
    }

    /**
     * 获得某个奖品信息(后台活动中奖记录)
     *
     * @param $awardId
     *
     * @return array
     *
     * [
     *      $reward_id => ['name' => '']
     *      ......
     * ]
     */
    public static function getAward($awardId)
    {
        $awardList = self::getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : [];
    }
}
