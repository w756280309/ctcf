<?php

namespace common\models\order;

use common\models\product\OnlineProduct;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bao_quan_queue".
 *
 * @property integer $id
 * @property integer $itemId
 * @property string  $itemType
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class BaoQuanQueue extends ActiveRecord
{
    const STATUS_SUSPEND = 0;//待处理
    const STATUS_SUCCESS = 1;//处理成功
    const STATUS_FAILED = -1;//处理失败

    const TYPE_LOAN = 'loan';//普通标的订单保全队列记录
    const TYPE_CREDIT_ORDER = 'credit_order';//买方债权订单保全记录队列
    const TYPE_CREDIT_NOTE = 'credit_note';//卖方资产被购买订单综合保全队列

    public static function tableName()
    {
        return 'bao_quan_queue';
    }

    public function rules()
    {
        return [
            [['itemId'], 'required'],
            [['itemId', 'created_at', 'updated_at', 'status'], 'integer'],
            [['itemType'], 'string'],
            [['itemId'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'itemId' => 'Item ID',
            'itemType' => 'Item Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    //获取合同编号
    public function getNum()
    {
        return str_pad($this->id, 10, '0', STR_PAD_LEFT);
    }
}
