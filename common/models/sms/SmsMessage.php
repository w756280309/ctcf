<?php

namespace common\models\sms;

use Yii;
use yii\behaviors\TimestampBehavior;

class SmsMessage extends \yii\db\ActiveRecord {

    const STATUS_WAIT = 0;
    const STATUS_SENT = 1; //已发送
    const STATUS_FAIL = 2; //失败

    const LEVEL_HIGH = 1;//等级高
    const LEVEL_MIDDLE = 2;
    const LEVEL_LOW = 3;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @表名
     */
    public static function tableName() {
        return 'sms_message';
    }

    /**
     * @验证规则
     */
    public function rules() {
        return [
            [['uid', 'template_id', 'mobile', 'message'], 'required'],
            ['status', 'default', 'value' => 0],
            ['level', 'default', 'value' => self::LEVEL_LOW]
        ];
    }

    /**
     * labels
     */
    public function attributeLabels() {
        return [
        ];
    }

}
