<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

class BatchpayItem extends \yii\db\ActiveRecord
{
    /**
     * 定义表名
     */
    public static function tableName()
    {
        return 'batchpay_item';
    }

    /**
     * 定义验证规则
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
    
    /**
     * 字段名
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
}
