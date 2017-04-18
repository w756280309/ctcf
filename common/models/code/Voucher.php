<?php

namespace common\models\code;

use yii\db\ActiveRecord;

/**
 * Class Voucher
 */
class Voucher extends ActiveRecord
{
    public function rules()
    {
        return [
            [['goodsType_sn', 'user_id', 'ref_type', 'ref_id'], 'required'],
            [['goodsType_sn', 'redeemIp'], 'string'],
            [['card_id', 'promo_id', 'user_id'], 'integer'],
            ['isRedeemed', 'boolean'],
            [['redeemTime', 'createTime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goodsType_sn' => '商品编号',
            'ref_type' => '关联类型',
            'ref_id' => '关联编号',
            'card_id' => '卡密ID',
            'promo_id' => '活动ID',
            'user_id' => '用户ID',
            'isRedeemed' => '是否领取',
            'redeemTime' => '领奖时间',
            'redeemIp' => '领取人IP',
            'createTime' => '创建时间',
        ];
    }
}
