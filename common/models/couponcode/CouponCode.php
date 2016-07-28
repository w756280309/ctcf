<?php

namespace common\models\couponcode;

use Yii;
use yii\db\ActiveRecord;

class CouponCode extends ActiveRecord
{
    public function rules()
    {
        return [
            ['code', 'string', 'length'=>16],
            ['code', 'unique'],
            [['user_id', 'isUsed'], 'integer'],
            ['isUsed', 'default', 'value'=>0],
            [['usedAt', 'createdAt', 'expiresAt'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '兑换码',
            'user_id' => 'User ID',
            'isUsed' => '已兑换',
            'usedAt' => '兑换时间',
            'createdAt' => '创建时间',
            'expiresAt' => '失效时间',
        ];
    }
}
