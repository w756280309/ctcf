<?php

namespace common\models\promo;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 */
class PromoLotteryTicket extends \yii\db\ActiveRecord
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
}
