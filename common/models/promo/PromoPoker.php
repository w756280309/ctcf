<?php

namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\order\OnlineOrder;
use common\models\user\CheckIn;
use common\models\user\User;

class PromoPoker extends BasePromo
{
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;

        //添加发牌 - 方块
        $this->deal($user, [
            'poker_type' => 'diamond',
            'issueTime' => (new \DateTime(date('Y-m-d H:i:s', $order->order_time))),
            'order_id' => $order->id,
        ]);
    }

    public function doAfterCheckIn(CheckIn $checkIn)
    {
        $user = User::findOne(['id' => $checkIn->user_id]);
        if (null === $user) {
            return false;
        }

        //添加发牌 - 梅花
        $this->deal($user, [
            'poker_type' => 'club',
            'issueTime' => (new \DateTime($checkIn->createTime)),
            'order_id' => null,
        ]);
    }

    /**
     * 发奖执行方法（可随机奖励积分）
     *
     * @param User   $user
     * @param Reward $reward
     *
     * @return bool
     * @throws \Exception
     */
    public function award(User $user, Reward $reward)
    {
        if (Reward::TYPE_RANDOM_POINT === $reward->ref_type) {
            $reward->ref_amount = mt_rand(6, 16);
        }

        return PromoService::award($user, $reward, $this->promo);
    }

    /**
     * 发牌
     *
     * @param User  $user 用户
     * @param array $data 牌面信息
     * [
     *      'poker_type' => 'spade', 'heart' or 'club' or 'diamond',
     *      'issueTime' => (new \DateTime()),
     *      'order_id' => null or 123,
     * ]
     *
     * @return bool
     */
    public function deal($user, $data)
    {
        try {
            $this->promo->isActive($user);
        } catch (NotActivePromoException $ex) {
            return false;
        }

        if (is_null($data['poker_type']) || is_null($user)) {
            return false;
        }
        $color = $data['poker_type'];
        if (!in_array($color, ['spade', 'heart', 'club', 'diamond'])) {
            return false;
        }
        $term = PokerUser::calcTerm(time());
        $poker_value = null;
        $model = PokerUser::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['term' => $term])
            ->one();
        if (null === $model) {
            $model = New PokerUser();
        }
        if ($model->{$color} > 0) {
            return false;
        }

        $model->user_id = $user->id;
        $model->term = $term;
        $issueTime = $data['issueTime']->format('Y-m-d H:i:s');

        //2017-09-25日上午10点采用新的方案
        switch ($color) {
            case 'spade':
                $model->spade = $this->createSpade($term);
                break;
            case 'heart':
                $model->firstVisitTime = $issueTime;
                break;
            case 'club':
                $model->checkInTime = $issueTime;
                break;
            case 'diamond':
                if (isset($data['order_id'])) {
                    $model->order_id = $data['order_id'];
                }
                break;
        }

        if ('spade' !== $color) {
            $poker_value = mb_substr($issueTime, -2) % 13;
            if ($poker_value == 0) {
                $poker_value += 13;
            }
            $model->{$color} = $poker_value;
        }

        return $model->save(false);
    }

    /**
     * 构造一个中奖
     */
    private function createSpade($term)
    {
        $pool = $this->createPool($term);

        return Reward::draw($pool);
    }

    private function createPool($term)
    {
        $winNumber = Poker::createWinningNumber($term);
        $keys = range(1, 13);
        $pool = [];

        foreach ($keys as $k => $v) {
            if ($winNumber === $v) {
                $pool[$v] = '0.4';
            } else {
                $pool[$v] = '0.05';
            }
        }

        return $pool;
    }
}
