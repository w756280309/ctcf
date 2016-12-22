<?php

namespace common\models\mall;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * 积分订单对象
 *
 * This is the model class for table "point_order".
 *
 * @property integer $id
 * @property string $sn             平台订单SN
 * @property string $orderNum       在兑吧的订单号
 * @property integer $user_id
 * @property integer $points        订单消耗积分
 * @property string $orderTime      订单时间（兑吧请求我们传的时间,订单一律以此为准）
 * @property integer $isPaid        是否已经扣除积分
 * @property integer $status        订单状态 0初始，1成功，-1失败，-2撤销,-3未知
 * @property string $mallUrl        兑吧发起订单请求的URL
 * @property integer $createdAt
 * @property integer $updatedAt
 */
class PointOrder extends \yii\db\ActiveRecord
{

    const STATUS_INIT = 0;      //初始订单
    const STATUS_SUCCESS = 1;   //成功订单
    const STATUS_FAIL = -1;     //失败订单
    const STATUS_CANCEL = -2;   //撤销订单
    const STATUS_OTHER = -3;    //异常订单

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'points', 'isPaid', 'status', 'createdAt', 'updatedAt'], 'integer'],
            [['orderTime'], 'safe'],
            [['sn'], 'string', 'max' => 32],
            [['orderNum', 'mallUrl'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'orderNum' => 'Order Num',
            'user_id' => 'User ID',
            'points' => 'Points',
            'orderTime' => 'Order Time',
            'isPaid' => 'Is Paid',
            'status' => 'Status',
            'mallUrl' => 'Mall Url',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
