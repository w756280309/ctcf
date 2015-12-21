<?php

namespace common\models\channel;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property integer $id
 * @property string $contract_name
 * @property string $contract_number
 * @property integer $contract_template_id
 * @property string $contract_content
 * @property integer $uid
 * @property string $order_sn
 * @property string $channel_user_sn
 * @property string $channel_order_sn
 */
class Contract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_template_id', 'uid'], 'integer'],
            [['contract_content'], 'string'],
            [['contract_name'], 'string', 'max' => 50],
            [['contract_number', 'order_sn', 'channel_user_sn', 'channel_order_sn'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract_name' => 'Contract Name',
            'contract_number' => 'Contract Number',
            'contract_template_id' => 'Contract Template ID',
            'contract_content' => 'Contract Content',
            'uid' => 'Uid',
            'order_sn' => 'Order Sn',
            'channel_user_sn' => 'Channel User Sn',
            'channel_order_sn' => 'Channel Order Sn',
        ];
    }
}
