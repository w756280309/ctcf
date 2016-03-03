<?php

namespace common\models\bank;

use Yii;

/**
 * This is the model class for table "config_ebank".
 *
 */
class ConfigEbank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config_ebank';
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