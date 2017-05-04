<?php

namespace common\models\promo;

use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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

    public static function initNew(User $user, RankingPromo $promo, $source = null)
    {
        return new self([
            'user_id' => $user->id,
            'source' => $source,
            'promo_id' => $promo->id,
            'ip' => Yii::$app->request->getUserIP(),
        ]);
    }
}
