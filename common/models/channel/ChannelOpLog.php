<?php

namespace common\models\channel;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "channel_op_log".
 *
 * @property integer $id
 * @property integer $op_type
 * @property string $channel_product_sn
 * @property string $channel_order_sn
 * @property string $params
 * @property string $result_code
 * @property string $result
 * @property integer $updated_at
 * @property integer $created_at
 */
class ChannelOpLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_op_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['op_type', 'updated_at', 'created_at'], 'integer'],
            [['channel_id'], 'required'],
            [['channel_product_sn', 'channel_order_sn'], 'string', 'max' => 30],
            [['result_code'], 'string', 'max' => 6]
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'op_type' => 'Op Type',
            'channel_product_sn' => 'Channel Product Sn',
            'channel_order_sn' => 'Channel Order Sn',
            'result_code' => 'Result Code',
            'result' => 'Result',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
