<?php

namespace common\models\bank;

/**
 * This is the model class for table "config_ebank".
 */
class EbankConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EbankConfig';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeBusiness','typePersonal'],'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'typePersonal'=>'个人网银充值',
            'typeBusiness'=>'企业网银充值'
        ];
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bankId']);
    }
}
