<?php

namespace common\models\checkaccount;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "checkaccount_cfca".
 *
 * @property integer $id
 * @property string $tx_date
 * @property integer $tx_type
 * @property string $tx_sn
 * @property string $tx_amount
 * @property string $payment_amount
 * @property string $institution_fee
 * @property string $bank_notification_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class CheckaccountCfca extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkaccount_cfca';
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
            [['tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time'], 'required'],
            [['tx_date'], 'safe'],
            [['tx_type', 'created_at', 'updated_at'], 'integer'],
            [['tx_amount', 'payment_amount', 'institution_fee'], 'number'],
            [['tx_sn'], 'string', 'max' => 30],
            [['bank_notification_time'], 'string', 'max' => 14],
            [['tx_sn'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tx_date' => 'Tx Date',
            'tx_type' => 'Tx Type',
            'tx_sn' => 'Tx Sn',
            'tx_amount' => 'Tx Amount',
            'payment_amount' => 'Payment Amount',
            'institution_fee' => 'Institution Fee',
            'bank_notification_time' => 'Bank Notification Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
