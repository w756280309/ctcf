<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

class OrderQueue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'OrderQueue';
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    public static function initForQueue(OnlineOrder $order)
    {
        if (OnlineOrder::STATUS_FALSE != $order->status) {
            throw new \Exception('订单状态不正确');
        }
        return new self([
            'orderSn' => $order->sn,
            'status' => 0,
        ]);
    }
    
    /**
     * 对应订单.
     *
     * @return order
     */
    public function getOrder()
    {
        return $this->hasOne(OnlineOrder::className(), ['sn' => 'orderSn']);
    }

}