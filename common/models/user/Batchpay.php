<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

class Batchpay extends \yii\db\ActiveRecord
{
    /**
     * 定义表名
     */
    public static function tableName()
    {
        return 'batchpay';
    }

    /**
     * 设置规则
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
     * 返回字段显示名
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
    /**
     * 
     * 返回 BatchItem
     */
    public function getItems(){
        return $this->hasMany(BatchpayItem::className(), ['batchpay_id' => 'id']);
    }
    
}
