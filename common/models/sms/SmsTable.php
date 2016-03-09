<?php

namespace common\models\sms;

use yii\db\ActiveRecord;

class SmsTable extends ActiveRecord
{
    const STATUS_USE = 1;
    const STATUS_UNUSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'mobile'], 'required'],
            [['created_at'], 'default',  'value' => time()],
            [['status'], 'default',  'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '短信码',
            'time_len' => '短信有效时长',
            'type' => '类型',
            'mobile' => '手机号',
            'end_time' => '截止日期',
            'created_at' => '创建时间',
        ];
    }
}
