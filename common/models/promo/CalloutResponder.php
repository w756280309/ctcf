<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

class CalloutResponder extends ActiveRecord
{
    public function rules()
    {
        return [
            [['open_id', 'ip'], 'string'],
            [['callout_id', 'promo_id', 'promo_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => '用户身份识别ID',
            'callout_id' => '召集ID',
            'ip' => 'IP地址',
            'order_id' => '订单ID',
            'promo_id' => '活动ID',
            'createTime' => '创建时间',
        ];
    }

    public static function initNew($openId, Callout $callout, $promoId = null)
    {
        return new self([
            'ip' => \Yii::$app->request->getUserIP(),
            'openid' => $openId,
            'callout_id' => $callout->id,
            'createTime' => date('Y-m-d H:i:s'),
            'promo_id' => $promoId,
        ]);
    }

    public static function findByCalloutId($calloutId)
    {
        return CalloutResponder::find()
            ->where(['callout_id' => $calloutId]);
    }
}
