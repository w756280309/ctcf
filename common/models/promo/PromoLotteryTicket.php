<?php

namespace common\models\promo;

use common\models\user\User;
use Faker\Provider\DateTime;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\Request;

/**
 * This is the model class for table "promo_lottery_ticket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $isDrawn
 * @property integer $isRewarded
 * @property integer $reward_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $rewardedAt
 * @property integer $drawAt
 * @property string $ip
 * @property string $source
 * @property int    $promo_id
 * @property int    $joinSequence  //参加活动序列
 * @property string $duobaoCode    //夺宝码
 * @property string $expiryTime
 */
class PromoLotteryTicket extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_lottery_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'isDrawn', 'isRewarded', 'reward_id', 'created_at', 'updated_at', 'rewardedAt', 'drawAt', 'promo_id'], 'integer'],
            [['ip', 'source'], 'string', 'max' => 30],
            [['expiryTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'isDrawn' => '是否已抽奖',
            'isRewarded' => '是否已发奖',
            'reward_id' => '奖品ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'rewardedAt' => '发奖时间',
            'drawAt' => '发奖时间',
            'ip' => 'Ip',
            'source' => '抽奖机会来源',
            'promo_id' => '活动ID',
            'expiryTime' => '过期时间',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getReward()
    {
        return $this->hasOne(Reward::class, ['id' => 'reward_id']);
    }

    public static function findLotteryByPromoId($promoId)
    {
        return PromoLotteryTicket::find()->where(['promo_id' => $promoId]);
    }

    public static function fetchOneActiveTicket($promo, $user, \DateTime $dateTime = null)
    {
        if (null === $dateTime) {
            $dateTime = new \DateTime();
        }
        return PromoLotteryTicket::find()
            ->where(['promo_id' => $promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => false])
            ->andFilterWhere(['>=', 'expiryTime', $dateTime->format('Y-m-d H:i:s')])
            ->one();
    }

    public static function initNew(User $user, RankingPromo $promo, $source = null, \DateTime $expiryTime = null, Request $request = null)
    {
        if (null === $expiryTime) {
            $expiryTime = new \DateTime($promo->endTime);
        }
        return new self([
            'user_id' => $user->id,
            'source' => $source,
            'promo_id' => $promo->id,
            'expiryTime' => $expiryTime->format('Y-m-d H:i:s'),
            'ip' => empty($request) ? '' : $request->getUserIP(),
        ]);
    }

    /**
     * 获取完整的七位夺宝码
     */
    public function getCode()
    {
        return 1000000 + $this->duobaoCode;
    }
}
