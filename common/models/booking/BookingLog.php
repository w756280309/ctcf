<?php

namespace common\models\booking;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "booking_log".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $pid
 * @property string $name
 * @property string $mobile
 * @property integer $fund
 * @property integer $created_at
 * @property integer $updated_at
 */
class BookingLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking_log';
    }

    /**
     * {@inheritdoc}
     */
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
            [['uid', 'pid', 'name', 'mobile', 'fund'], 'required'],
            [['uid', 'pid', 'fund', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['mobile'], 'string', 'length' => 11],
            [['mobile'], 'match', 'pattern' => '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/', 'message' => '手机号格式错误'],
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
            'pid' => '项目ID',
            'name' => '姓名',
            'mobile' => '手机号',
            'fund' => '预约金额',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
