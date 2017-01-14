<?php

namespace common\models\user;

use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "coins_record".
 *
 * @property integer $id            ID
 * @property integer $user_id       用户ID
 * @property integer $order_id      订单ID
 * @property integer $incrCoins     财富值增量
 * @property integer $finalCoins    财富值总和
 * @property string  $createTime    创建时间
 * @property boolean $isOffline     是否线下
 */
class CoinsRecord extends ActiveRecord
{
    /**
     * 获取线上对应的订单信息.
     */
    public function getOnlineOrder()
    {
        return $this->hasOne(OnlineOrder::class, ['id' => 'order_id']);
    }

    /**
     * 获取线下对应的订单信息.
     */
    public function getOfflineOrder()
    {
        return $this->hasOne(OfflineOrder::class, ['id' => 'order_id']);
    }
}
