<?php

namespace common\models\tx;

use common\models\user\User;
use common\utils\SecurityUtils;
use Yii;
use Zii\Model\ActiveRecord;

class SmsMessage extends ActiveRecord
{
    const STATUS_INIT = 0;    //初始状态
    const STATUS_SUCCESS = 1; //发送成功
    const STATUS_FAIL = 2;    //发送失败

    const LEVEL_HIGH = 1;     //等级高
    const LEVEL_MIDDLE = 2;   //等级中
    const LEVEL_LOW = 3;      //等级低

    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return 'sms_message';
    }

    public function initNew(User $user, $templateId, array $data = [], $level = SmsMessage::LEVEL_MIDDLE)
    {
        $smsMessage = new self([
            'uid' => $user->id,
            'template_id' => $templateId,
            'safeMobile' => $user->safeMobile,
            'message' => json_encode($data),
            'level' => $level,
            'created_at' => time(),
        ]);

        $smsWhiteList = Yii::$app->params['sms_white_list'];
        if (Yii::$app->params['mock_sms'] && !in_array(SecurityUtils::decrypt($user->safeMobile), $smsWhiteList)) {
            $smsMessage->status = self::STATUS_SUCCESS;
        }

        return $smsMessage;
    }
}
