<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\affiliation\Affiliator;
use Zii\Validator\CnIdCardValidator;

/**
 * This is the model class for table "offline_order".
 *
 * @property integer $id
 * @property integer $affiliator_id 分销商ID
 * @property integer $user_id       用户ID
 * @property integer $loan_id       线下产品ID
 * @property string  $realName      姓名
 * @property string  $mobile        联系电话
 * @property string  $money         购买金额
 * @property string  $orderDate     认购日期
 * @property string  $created_at    创建时间
 * @property string  $isDeleted     是否删除
 * @property string  $idCard        身份证号
 * @property string  $accBankName   开户行名称
 * @property string  $bankCardNo    银行卡号
 * @property string  $valueDate     起息日
 * @property string  $apr           利率
 */
class OfflineOrder extends ActiveRecord
{
    public $realName;
    public function scenarios()
    {
        return [
            'confirm' => ['valueDate'],
            'edit' => ['realName', 'accBankName', 'bankCardNo','mobile', 'apr'],
            'default' => ['affiliator_id', 'loan_id',  'mobile', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo', 'apr'],
        ];
    }

    public function rules()
    {
        return [
            [['affiliator_id', 'loan_id', 'mobile', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo', 'apr'], 'required'],
            [['user_id', 'affiliator_id', 'loan_id', 'created_at'], 'integer'],
            [['realName', 'accBankName', 'bankCardNo','mobile'], 'required', 'on' => 'edit'],
            ['mobile', 'string', 'max' => 20],
            [['idCard', 'bankCardNo'], 'string', 'max' => 30],
            [['idCard'], CnIdCardValidator::className()],
            ['money', 'number'],
            [['orderDate', 'valueDate'], 'safe'],
            ['apr', 'number', 'min' => '0.0001', 'max' => '1'],
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
            'money' => '购买金额',//以万元为单位
            'orderDate' => '订单日期',
            'created_at' => '创建时间',
            'isDeleted' => '是否删除',
            'idCard' => '身份证号',
            'accBankName' => '开户行名称',
            'bankCardNo' => '银行卡号',
            'valueDate' => '起息日',
            'apr' => '利率',
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

    public function getOrder_money()
    {
        return $this->money;
    }

    /**
     * 获取认购日期
     */
    public function getOrderDate()
    {
        return $this->orderDate;
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
    //分期还款最后一期
    public function getLastTerm()
    {
        return OfflineRepaymentPlan::find()->where(['order_id' => $this->id])->count();
    }
}
