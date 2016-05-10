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
 * @property integer $proId
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class BaoQuanQueue extends ActiveRecord
{
    public static function tableName()
    {
        return 'bao_quan_queue';
    }

    public function rules()
    {
        return [
            [['proId'], 'required'],
            [['proId', 'created_at', 'updated_at', 'status'], 'integer'],
            [['proId'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proId' => 'Pro ID',
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
}
