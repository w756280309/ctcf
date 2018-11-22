<?php

namespace common\models\bank;

/**
 * This is the model class for table "qpaydepute_config".
 */
class QpayDeputeConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qpaydepute_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['isDisabled', 'allowBind'], 'integer'],
            [['dailyLimit', 'singleLimit'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'isDisabled' => '商业委托快捷充值',
            'allowBind' => '商业委托快捷绑卡',
            'singleLimit' => '万/次',
            'dailyLimit' => '万/天',
        ];
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bankId']);
    }
}
