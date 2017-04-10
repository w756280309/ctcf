<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

class Reward extends ActiveRecord
{
    const TYPE_PIKU = 'PIKU'; //实物奖品
    const TYPE_POINT = 'POINT'; //积分
    const TYPE_COUPON = 'COUPON'; //代金券

    public function rules()
    {
        return [
            ['sn', 'unique'],
            ['name', 'string', 'max' => 100],
            [['limit', 'promo_id', 'ref_id'], 'integer'],
            [['ref_type', 'path'], 'string'],
            ['ref_amount', 'number'],
            ['createTime', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '奖品sn',
            'name' => '奖品名称',
            'limit' => '奖品数量',
            'ref_type' => '类型',
            'ref_amount' => '面值',
            'path' => '图片路径',
            'promo_id' => '活动ID',
            'createTime' => '创建时间',
            'ref_id' => '奖品关联ID',
        ];
    }
}