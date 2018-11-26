<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "user_20181225_draw_record".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $type
 * @property integer $draw_nums
 * @property integer $created_at
 * @property integer $updated_at
 */
class User20181225DrawRecord extends \yii\db\ActiveRecord
{
    const TYPE_DRAW = 1;//抽奖
    const TYPE_SHARE = 2;//分享
    const TYPE_MORE = 3;//用户单笔出借金额大于5000
    const TYPE_INVITE = 4;//用户邀请并出借
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_20181225_draw_record';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'draw_nums', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'integer', 'min' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'type' => '类型',
            'draw_nums' => '次数变动',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getTxDate()
    {
        return date('Ymd', $this->created_at);
    }
}
