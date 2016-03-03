<?php

namespace common\models\bank;

use Yii;

/**
 * This is the model class for table "bank_card_bin".
 *
 */
class BankCardBin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_card_bin';
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