<?php

namespace common\models\user;

use common\models\order\OnlineOrder;
use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "user_asset".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $loan_id
 * @property string $amount
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserAsset extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_asset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'order_id', 'loan_id', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'order_id' => '订单ID',
            'loan_id' => '标的ID',
            'amount' => '金额',
            'created_at' => '新建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 同步订单信息到用户资源表
     * @param OnlineOrder $order
     * @return UserAsset
     */
    public static function initUserAssetFromOrder(OnlineOrder $order)
    {
        $asset = new self([
            'user_id' => $order->uid,
            'order_id' => $order->id,
            'loan_id' => $order->online_pid,
            'amount' => $order->order_money,
            'created_at' => $order->order_time,
            'updated_at' => $order->created_at
        ]);
        return $asset;
    }
}
