<?php

namespace common\models\code;

use common\models\user\User;
use yii\db\ActiveRecord;

class Code extends ActiveRecord
{
    const TYPE_COUPON = 1;
    const TYPE_GOODS = 2;

    const REF_TYPE_HISTORY_CODE = 'virtual_card.code';

    public function rules()
    {
        return [
            ['code', 'string', 'length'=>16],
            ['code', 'unique'],
            ['goodsType_sn', 'string'],
            [['user_id', 'isUsed', 'goodsType'], 'integer'],
            ['isUsed', 'default', 'value' => 0],
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
            'goodsType_sn' => '商品sn',
            'goodsType' => '商品类型', //type=1代金券 type=2实体商品
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function createCode()
    {
        $code = '';
        $str = "BCEFGHJKMPQRTVWXY2346789";
        $length = strlen($str) - 1;
        for ( $i = 0; $i < 16; $i++ ) {
            $code .= substr($str, mt_rand(0, $length), 1);
        }
        return $code;
    }
}
