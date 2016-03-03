<?php

namespace common\models\bank;

use Yii;

/**
 * This is the model class for table "config_qpay".
 *
 */
class ConfigQpay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config_qpay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bankId']);
    }
    
}