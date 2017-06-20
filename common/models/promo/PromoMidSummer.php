<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\transfer\Transfer;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class PromoMidSummer
{
    public $promo;
    private $promoConfig;
    private $annualInvestLimit = 100000;//活动期间累计年化每达到100000赠送一次抽奖机会
    private $ticketLimit = 3;
    const SOURCE_ORDER = 'order';//购买

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        $this->promoConfig = json_decode($promo->config, true);
    }

    /**
     * 订单完成之后统一调用逻辑
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promo = $this->promo;
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS
            || !$promo->isActive($user, $order->order_time)
        ) {
            return;
        }

        $tickets = (int) PromoLotteryTicket::findLotteryByPromoId($promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => self::SOURCE_ORDER])
            ->count();
        if ($tickets > $this->ticketLimit) {
            return;
        }

        $startTime = new \DateTime($promo->startTime);
        $endTime = new \DateTime($promo->endTime);
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
        $actualInvestLimit = intval($annualInvest / $this->annualInvestLimit);
        $allTicket = $actualInvestLimit >= $this->ticketLimit ? $this->ticketLimit : $actualInvestLimit;
        $extraTicket = max($allTicket - $tickets, 0);
        for ($i = 1; $i <= $extraTicket; $i++) {
            PromoLotteryTicket::initNew($user, $promo, self::SOURCE_ORDER)->save(false);
        }
    }

    /**
     * 抽奖
     *
     * @param User $user
     * @return PromoLotteryTicket
     * @throws \Exception
     */
    public function draw(User $user)
    {
        //判断该用户是否参与了活动
        $promo = $this->promo;
        $promo->isActive($user);

        //根据用户累计年化选择对应的奖池设置数组（private）
        $poolSetting = $this->getPool($user, $promo);

        //根据奖池设置数组获得一个奖品sn(reward::draw($poolSetting))
        $sn = Reward::draw($poolSetting);
        if (false === $sn) {
            throw new \Exception('2000:未抽到奖项，抽奖失败，请联系客服!');
        }
        $reward = Reward::findOne(['sn' => $sn]);
        if (null === $reward) {
            throw new \Exception('2001:无法获得奖品信息，抽奖失败，请联系客服!');
        }

        //抽奖环节
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //LotteryTicket
            $sql = "select * from promo_lottery_ticket where user_id = :userId and promo_id = :promoId and isDrawn = :isDrawn limit 1";
            $lottery = $db->createCommand($sql, [
                'userId' => $user->id,
                'promoId' => $promo->id,
                'isDrawn' => false,
            ])->queryOne();
            if (empty($lottery)) {
                throw new \Exception('0000:无抽奖机会');
            }

            //更新LotteryTicket
            $sql = "update promo_lottery_ticket set reward_id = :rewardId, isDrawn = :isDrawn, drawAt = :drawAt where id = :lotteryId and isDrawn = FALSE";
            $affectRows = $db->createCommand($sql, [
                'isDrawn' => true,
                'drawAt' => time(),
                'rewardId' => $reward->id,
                'lotteryId' => $lottery['id'],
            ])->execute();
            if (!$affectRows) {
                throw new \Exception('0001:抽奖失败，请联系客服!');
            }

            //减库存操作
            if (!Reward::decStoreBySn($sn)) {
                $transaction->rollBack();
                throw new \Exception('0002:抽奖失败，请联系客服!');
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }

        //发奖环节，调用Transfer的AR类init方法初始化并保存（后台脚本执行发红包）
        $lottery = $this->reward($user, $reward, $lottery['id']);

        return $lottery;
    }

    private function reward($user, $reward, $lotteryId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $lottery = PromoLotteryTicket::findOne($lotteryId);
        try {
            //更新抽奖机会的发奖状态
            if ($lottery->isRewarded) {
                throw new \Exception('1000:已发奖');
            }
            $lottery->isRewarded = true;
            $lottery->rewardedAt = time();
            $lottery->save(false);

            //将记录写入红包队列transfer表
            $metadata['promo_id'] = $this->promo->id;
            Transfer::initNew($user, $reward->ref_amount, $metadata)->save(false);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $lottery;
    }

    private function getPool($user, $promo)
    {
        $startTime = new \DateTime($promo->startTime);
        $endTime = new \DateTime($promo->endTime);
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
        $config = $this->promoConfig;
        $annualInvestLimit = $config['investLimit'];
        if ($annualInvest <= $annualInvestLimit) {
            return $config['lower600Thousand'];
        }

        return $config['higher600Thousand'];
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
        $promo = RankingPromo::findOne(['key' => 'promo_170621']);
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
     * @param int $awardId 不需额外校验
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

    /**
     * 获得某个人已中奖列表
     *
     * @param User $user 无需格外校验
     *
     * @return array
     */
    public function getRewardedList(User $user)
    {
        return PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isRewarded' => true])
            ->orderBy('drawAt desc')
            ->all();
    }
}
