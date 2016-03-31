<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "online_fangkuan_detail".
 *
 * @property string $id
 * @property int $fangkuan_order_id
 * @property int $product_order_id
 * @property string $order_money
 * @property int $online_product_id
 * @property string $order_time
 * @property int $status
 * @property string $admin_id
 * @property string $create_at
 * @property string $updated_at
 */
class OnlineFangkuanDetail extends \yii\db\ActiveRecord
{
    use \Zii\Model\ErrorExTrait;

    public static function createSN($pre = 'fkd')
    {
        $pre_val = Yii::$app->params['bill_prefix'][$pre];
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_fangkuan_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fangkuan_order_id', 'product_order_id', 'online_product_id', 'order_time', 'admin_id'], 'required'],
            [['fangkuan_order_id', 'product_order_id', 'online_product_id', 'status', 'admin_id'], 'integer'],
            [['order_money'], 'number'],
            [['order_time'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fangkuan_order_id' => 'Fangkuan Order ID',
            'product_order_id' => 'Product Order ID',
            'order_money' => 'Order Money',
            'online_product_id' => 'Online Product ID',
            'order_time' => 'Order Time',
            'status' => 'Status',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
