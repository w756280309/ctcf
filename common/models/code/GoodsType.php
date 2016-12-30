<?php

namespace common\models\code;

use yii\db\ActiveRecord;

class GoodsType extends ActiveRecord
{
    public function rules()
    {
        return [
            [['sn', 'name'], 'string'],
            [['sn', 'name'], 'required'],
            ['sn', 'unique', 'message' => '商品类型应唯一'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '商品sn',
        ];
    }
}