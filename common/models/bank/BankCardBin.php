<?php

namespace common\models\bank;

/**
 * This is the model class for table "bank_card_bin".
 */
class BankCardBin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bankcardbin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
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
