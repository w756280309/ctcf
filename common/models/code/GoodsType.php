<?php

namespace common\models\code;

use yii\db\ActiveRecord;

class GoodsType extends ActiveRecord
{
    public function rules()
    {
        return [
            ['sn', 'string'],
            ['name', 'required'],
            ['name', 'string', 'max' => 15],
            ['sn', 'unique', 'message' => '此代金券已经被商品添加！'],
            [['type', 'effectDays', 'affiliator_id'], 'integer'],
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
            'effectDays' => '有效期天数',
            'affiliator_id' => '合作方ID',
            'isSkuEnabled' => '是否开启SKU',
            'stock' => '库存数量',
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

    //根据ｓｎ获取商品
    public static function fetchOne($goodsTypeSn)
    {
        //todo $goodsTypeSn 是否拼接
        return GoodsType::findOne(['sn' => $goodsTypeSn]);

    }
}
