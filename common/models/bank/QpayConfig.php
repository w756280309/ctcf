<?php

namespace common\models\bank;

/**
 * This is the model class for table "config_qpay".
 */
class QpayConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qpayconfig';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['isDisabled', 'integer'],
            [['dailyLimit', 'singleLimit'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'isDisabled' => '快捷充值（wap端绑卡）',
            'singleLimit' => '万/次',
            'dailyLimit' => '万/天',
        ];
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bankId']);
    }
}
