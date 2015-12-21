<?php

namespace common\models\checkaccount;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "checkaccount_wdjf".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $tx_date
 * @property integer $tx_type
 * @property string $tx_sn
 * @property string $tx_amount
 * @property string $payment_amount
 * @property string $institution_fee
 * @property string $bank_notification_time
 * @property integer $is_checked
 * @property integer $is_auto_okay
 * @property integer $is_okay
 * @property integer $created_at
 * @property integer $updated_at
 */
class CheckaccountWdjf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkaccount_wdjf';
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
    public function rules()
    {
        return [
            [['order_no', 'tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time'], 'required'],
            [['tx_date'], 'safe'],
            [['tx_type', 'is_checked', 'is_auto_okay', 'is_okay', 'created_at', 'updated_at'], 'integer'],
            [['tx_amount', 'payment_amount', 'institution_fee'], 'number'],
            [['order_no', 'tx_sn'], 'string', 'max' => 30],
            [['bank_notification_time'], 'string', 'max' => 14]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'tx_date' => 'Tx Date',
            'tx_type' => 'Tx Type',
            'tx_sn' => 'Tx Sn',
            'tx_amount' => 'Tx Amount',
            'payment_amount' => 'Payment Amount',
            'institution_fee' => 'Institution Fee',
            'bank_notification_time' => 'Bank Notification Time',
            'is_checked' => 'Is Checked',
            'is_auto_okay' => 'Is Auto Okay',
            'is_okay' => 'Is Okay',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
