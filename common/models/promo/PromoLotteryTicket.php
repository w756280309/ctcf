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
 * @property string $ip
 * @property integer $source
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
            [['user_id', 'isDrawn', 'isRewarded', 'reward_id', 'created_at', 'updated_at', 'rewardedAt', 'source'], 'integer'],
            [['ip'], 'string', 'max' => 30]
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
            'isDrawn' => 'Is Drawn',
            'isRewarded' => 'Is Rewarded',
            'reward_id' => 'Reward ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'rewardedAt' => 'Rewarded At',
            'ip' => 'Ip',
            'source' => '抽奖机会来源'//0表示初始化，1新增满5万，2累计满20万，3再来一次
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }
}
