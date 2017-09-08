<?php

namespace common\models\sms;

use yii\behaviors\TimestampBehavior;
use common\models\user\User;

/**
 * Class SmsMessage
 * @package common\models\sms
 *
 * @property int    $template_id
 * @property string $message
 * @property int    $uid
 * @property int    $created_at
 * @property string $serviceProvider
 */
class SmsMessage extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 0;
    const STATUS_SENT = 1; //已发送
    const STATUS_FAIL = 2; //失败

    const LEVEL_HIGH = 1;//等级高
    const LEVEL_MIDDLE = 2;
    const LEVEL_LOW = 3;

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
     * @表名
     */
    public static function tableName()
    {
        return 'sms_message';
    }

    /**
     * @验证规则
     */
    public function rules()
    {
        return [
            [['uid', 'template_id', 'message'], 'required'],
            ['status', 'default', 'value' => 0],
            ['level', 'default', 'value' => self::LEVEL_LOW],
            ['safeMobile', 'string'],
        ];
    }

    /**
     * labels.
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * 初始化短信对象
     *
     * @param User  $user
     * @param array $message
     * @param type  $template_id
     * @param type  $level
     *
     * @return smsMessage
     */
    public static function initSms(User $user, array $message, $template_id, $level = self::LEVEL_MIDDLE)
    {
        $smsmsg = new self([
            'uid' => $user->id,
            'template_id' => $template_id,
            'safeMobile' => $user->safeMobile,
            'level' => $level,
            'message' => json_encode($message),
        ]);

        return $smsmsg;
    }
}
