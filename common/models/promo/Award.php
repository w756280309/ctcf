<?php

namespace common\models\promo;

use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\user\MoneyRecord;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use yii\db\ActiveRecord;

/**
 * Class Award
 * @package common\models\promo
 *
 * @property int        $id
 * @property int        $user_id
 * @property string     $createTime
 * @property int        $promo_id
 * @property int        $ticket_id
 * @property float      $amount
 * @property string     $ref_type
 * @property int        $ref_id
 */
class Award extends ActiveRecord
{
    const TYPE_COUPON = 'coupon';//对应 ref_id 存放 user_coupon_id
    const TYPE_CASH = 'cash';//对应 ref_id 存放 money_record_id
    const TYPE_POINTS = 'points';//对应 ref_id 存放 point_record_id
    const TYPE_GOODS = 'goods';//对应 ref_id 存放 goods_type_id

    public static function tableName()
    {
        return 'award';
    }

    /**
     * 初始化获奖记录
     * @param User $user
     * @param RankingPromo $promo
     * @param PromoLotteryTicket|null $ticket
     * @return Award
     */
    public static function initNew(User $user, RankingPromo $promo, PromoLotteryTicket $ticket = null)
    {
        return new self([
            'user_id' => $user->id,
            'createTime' => date('Y-m-d H:i:s'),
            'promo_id' => $promo->id,
            'ticket_id' => is_null($ticket) ? null : $ticket->id,
        ]);
    }

    /**
     * 初始化代金券类奖励
     *
     * @param User $user
     * @param RankingPromo $promo
     * @param UserCoupon $userCoupon
     * @param PromoLotteryTicket|null $ticket
     * @return Award
     */
    public static function couponAward(User $user, RankingPromo $promo, UserCoupon $userCoupon, PromoLotteryTicket $ticket = null)
    {
        $award = self::initNew($user, $promo, $ticket);
        $award->amount = $userCoupon->couponType->amount;
        $award->ref_type = self::TYPE_COUPON;
        $award->ref_id = $userCoupon->id;

        return $award;
    }

    /**
     * 初始化现金红包券类奖励
     *
     * @param User $user
     * @param RankingPromo $promo
     * @param MoneyRecord $moneyRecord
     * @param PromoLotteryTicket|null $ticket
     * @return Award
     */
    public static function cashAward(User $user, RankingPromo $promo, MoneyRecord $moneyRecord, PromoLotteryTicket $ticket = null)
    {
        $award = self::initNew($user, $promo, $ticket);
        $award->amount = $moneyRecord->in_money;
        $award->ref_type = self::TYPE_CASH;
        $award->ref_id = $moneyRecord->id;

        return $award;
    }

    /**
     * 初始化积分类奖励
     *
     * @param User $user
     * @param RankingPromo $promo
     * @param PointRecord $pointRecord
     * @param PromoLotteryTicket|null $ticket
     * @return Award
     */
    public static function pointsAward(User $user, RankingPromo $promo, PointRecord $pointRecord, PromoLotteryTicket $ticket = null)
    {
        $award = self::initNew($user, $promo, $ticket);
        $award->amount = $pointRecord->incr_points;
        $award->ref_type = self::TYPE_POINTS;
        $award->ref_id = $pointRecord->id;

        return $award;
    }
}