<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "online_repayment_record".
 *
 * @property string $id
 * @property string $online_pid
 * @property integer $order_id
 * @property string $order_sn
 * @property integer $qishu
 * @property string $uid
 * @property string $benxi
 * @property string $benjin
 * @property string $lixi
 * @property string $overdue
 * @property string $yuqi_day
 * @property string $benxi_yue
 * @property string $refund_time
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class OnlineRepaymentRecord extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 0;//未还
    const STATUS_DID = 1;//已还
    const STATUS_BEFORE = 2;//提前
    const STATUS_FALSE = 3;//无效
    
    public static function createSN($pre = 'hkjl'){
        $pre_val = 'HB';
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float)$usec + (float)$sec);
        
        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999),$usec);
        return $pre_val.str_replace('x', $sec, $date);
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'online_repayment_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['online_pid', 'order_id', 'qishu', 'uid', 'refund_time', 'status'], 'integer'],
            ['online_pid', 'required'],
            [['benxi', 'benjin', 'lixi', 'overdue', 'benxi_yue'], 'number'],
            [['overdue', 'yuqi_day'], 'default', 'value' => 0],
            [['order_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'online_pid' => 'Online Pid',
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'qishu' => 'Qishu',
            'uid' => 'Uid',
            'benxi' => 'Benxi',
            'benjin' => 'Benjin',
            'lixi' => 'Lixi',
            'overdue' => 'Overdue',
            'yuqi_day' => 'Yuqi Day',
            'benxi_yue' => 'Benxi Yue',
            'refund_time' => 'Refund Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
