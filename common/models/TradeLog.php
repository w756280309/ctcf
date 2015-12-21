<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class TradeLog extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trade_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
    
    public function behaviors() {
        return [
             TimestampBehavior::className(),
        ];
    }
    
    public static function eventTest($parm){
        var_dump($parm);
        //echo "you should :".$parm->data.'<br>';
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tx_code' => 'tx_code',
            'tx_sn' => 'tx_sn',
            'pay_id' => 'pay_id',
            'type' => 'Type',
            'uid' => 'Uid',
            'account_id' => 'Account ID',
            'request' => 'request',
            'response_code' => 'Response Code',
            'response' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
