<?php

namespace common\models\checkaccount;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "checkaccount_hz".
 *
 * @property integer $id
 * @property string $tx_date
 * @property integer $recharge_count
 * @property string $recharge_sum
 * @property integer $jiesuan_count
 * @property string $jiesuan_sum
 * @property integer $created_at
 * @property integer $updated_at
 */
class CheckaccountHz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkaccount_hz';
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
            [['tx_date', 'recharge_count', 'recharge_sum', 'jiesuan_count', 'jiesuan_sum'], 'required'],
            [['tx_date'], 'safe'],
            [['recharge_count', 'jiesuan_count', 'created_at', 'updated_at'], 'integer'],
            [['recharge_sum', 'jiesuan_sum'], 'number']
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
            'recharge_count' => 'Recharge Count',
            'recharge_sum' => 'Recharge Sum',
            'jiesuan_count' => 'Jiesuan Count',
            'jiesuan_sum' => 'Jiesuan Sum',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
