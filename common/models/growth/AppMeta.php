<?php

namespace common\models\growth;

use yii\db\ActiveRecord;
use yii\web\Request;

class AppMeta extends ActiveRecord
{
    public function rules()
    {
        return [
            ['key', 'unique', 'message' => 'key应唯一'],
            [['value', 'name'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'key' => 'KEY',
            'value' => 'VALUE',
        ];
    }


    /**
     * 根据key获得value值信息
     *
     * @param  $key string
     *
     * @return null|string
     */
    public static function getValue($key)
    {
        $appMeta = AppMeta::findOne(['key' => $key]);
        return null !== $appMeta ? $appMeta->value : null;
    }
}
