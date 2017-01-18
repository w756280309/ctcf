<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\affiliation\Affiliator;

class OfflineOrder extends ActiveRecord
{
    public function scenarios()
    {
        return [
            'confirm' => ['valueDate'],
            'default' => ['affiliator_id', 'loan_id', 'realName', 'mobile', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo'],
        ];
    }

    public function rules()
    {
        return [
            [['affiliator_id', 'loan_id', 'realName', 'mobile', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo'], 'required'],
            [['user_id', 'affiliator_id', 'loan_id', 'created_at'], 'integer'],
            ['realName', 'string', 'max' => 50],
            ['mobile', 'string', 'max' => 20],
            [['idCard', 'bankCardNo'], 'string', 'max' => 30],
            ['money', 'number'],
            [['orderDate', 'valueDate'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'affiliator_id' => '分销商ID',
            'user_id' => '用户ID',
            'loan_id' => '线下产品ID',
            'realName' => '姓名',
            'mobile' => '联系电话',
            'money' => '购买金额',
            'orderDate' => '订单日期',
            'created_at' => '创建时间',
            'isDeleted' => '是否删除',
            'idCard' => '身份证号',
            'accBankName' => '开户行名称',
            'bankCardNo' => '银行卡号',
            'valueDate' => '起息日',
        ];
    }

    public function getAffliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }

    public function getLoan()
    {
        return $this->hasOne(OfflineLoan::className(), ['id' => 'loan_id']);
    }

    public function getUser()
    {
        return $this->hasOne(OfflineUser::className(), ['id' => 'user_id']);
    }

    /**
     * 根据订单计算年化投资金额.
     */
    public function getAnnualInvestment()
    {
        if (strpos($this->loan->unit, '天') !== false) {
            $base = 365;
        } else {
            $base = 12;
        }

        return bcdiv(bcmul($this->money * 10000, $this->loan->expires, 14), $base, 2);
    }
}
