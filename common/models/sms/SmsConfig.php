<?php

namespace common\models\sms;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sms_config".
 *
 * @property integer $id
 * @property string $template_id 短信模板ID
 * @property string $config      短信内容配置信息,是json格式的字符串
 */
class SmsConfig extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'config'], 'required'],
            [['config'], 'string'],
            [['template_id'], 'string', 'max' => 255],
        ];
    }

    public function getConfig()
    {
        return json_decode($this->config, true);
    }
}
