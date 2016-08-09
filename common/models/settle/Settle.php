<?php

namespace common\models\settle;

use yii\db\ActiveRecord;

class Settle extends ActiveRecord
{
    const TXTYPE_RECHARGE = 1; //充值
    const TXTYPE_DRAW = 2;     //提现
    const TXTYPE_REALNAME = 6; //实名认证

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settle';
    }

    public function rules()
    {
        return [
            [['txSn', 'txDate', 'money', 'fee', 'serviceSn', 'txType'], 'required'],
            ['txDate', 'safe'],
            [['txSn', 'serviceSn'], 'string', 'max' => 60],
            [['money', 'fee'], 'number'],
            [['txType', 'status'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txSn' => '对账单号',
            'txDate' => '订单日期',
            'money' => '订单金额',
            'fee' => '手续费',
            'serviceSn' => '联动交易流水号',
            'txType' => '对账类型',
        ];
    }
}