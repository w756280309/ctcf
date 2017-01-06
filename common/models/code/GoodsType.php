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
            ['type', 'integer'],
            ['createdAt', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '商品sn',
            'type' => '商品类型',
            'createdAt' => '创建时间',
        ];
    }

    public function getCode()
    {
        return $this->hasMany(Code::className(), ['goodsType_sn' => 'sn']);
    }

    /**
     * 生成实体商品sn
     */
    public static function createGiftSn()
    {
        return 'GIFT' . date('YmdHis') . rand(1000, 9999);
    }
}
