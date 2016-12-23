<?php

namespace common\models\mall;

use common\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * 积分订单对象
 *
 * This is the model class for table "point_order".
 *
 * @property integer $id
 * @property string $sn             平台订单SN
 * @property string $type           商品类型【全小写】
 * @property string $orderNum       在兑吧的订单号
 * @property integer $user_id
 * @property integer $points        订单消耗积分
 * @property string $orderTime      订单时间（兑吧请求我们传的时间,订单一律以此为准）
 * @property integer $isPaid        是否已经扣除积分
 * @property integer $status        订单状态 0初始，1成功，-1失败，-2撤销,-3未知
 * @property integer $created_at
 * @property integer $updated_at
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
            [['user_id', 'points', 'isPaid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['orderTime'], 'safe'],
            [['sn'], 'string', 'max' => 32],
            [['orderNum', 'type'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'orderNum' => 'Order Num',
            'user_id' => 'User ID',
            'points' => 'Points',
            'orderTime' => 'Order Time',
            'isPaid' => 'Is Paid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
