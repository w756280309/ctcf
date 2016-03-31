<?php

namespace common\models\booking;

use Zii\Validator\CnMobileValidator;
use yii\behaviors\TimestampBehavior;

/**
 * 预约记录
 *
 * @property int $id
 * @property int $uid
 * @property int $pid
 * @property string $name
 * @property string $mobile
 * @property int $fund
 * @property int $created_at
 * @property int $updated_at
 */
class BookingLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'name', 'mobile', 'fund'], 'required'],
            [['uid', 'pid', 'fund', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            ['mobile', CnMobileValidator::className(), 'skipOnEmpty' => false],
        ];
    }

    /**
     * {@inheritdoc}
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
