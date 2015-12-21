<?php

namespace common\models\channel;

use Yii;

/**
 * This is the model class for table "channel_product".
 *
 * @property integer $id
 * @property string $product_sn
 * @property string $channel_id
 * @property string $channel_product_sn
 * @property integer $count
 */
class ChannelProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['channel_id', 'channel_product_sn', 'count'], 'required'],
            [['count'], 'integer'],
            [['product_sn', 'channel_id', 'channel_product_sn'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_sn' => 'Product Sn',
            'channel_id' => 'Channel ID',
            'channel_product_sn' => 'Channel Product Sn',
            'count' => 'Count',
        ];
    }
}
