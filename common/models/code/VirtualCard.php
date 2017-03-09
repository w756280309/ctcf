<?php
namespace common\models\code;

use yii\db\ActiveRecord;

class VirtualCard extends ActiveRecord
{
    public function rules()
    {
        return [
            [['serial', 'goodsType_id'], 'required'],
            [['serial', 'secret'], 'unique'],
            [['isPull', 'isUsed', 'user_id'], 'integer'],
            [['pullTime', 'usedTime', 'createTime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serial' => '券码',
            'secret' => '密码',
            'user_id' => 'User ID',
            'isPull' => '已领取',
            'pullTime' => '领取时间',
            'isUsed' => '已使用',
            'usedTime' => '使用时间',
            'createTime' => '创建时间',
            'goodsType_id' => '商品ID',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(GoodsType::className(), ['id' => 'goodsType_id']);
    }
}
