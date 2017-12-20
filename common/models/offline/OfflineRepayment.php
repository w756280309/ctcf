<?php

namespace common\models\offline;

use common\models\offline\OfflineLoan;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "repayment".
 *
 * @property integer $id
 * @property integer $loan_id   标的ID
 * @property integer $term  分期期数
 * @property string $dueDate    预期还款时间
 * @property string $amount    应还总金额
 * @property string $principal  本金
 * @property string $interest   利息
 * @property integer $isRepaid  是否还款，指融资用户扣钱
 * @property string $repaidAt  还款时间 dateTime类型
 * @property integer $isRefunded  是否回款,指给用户转钱 => 本期是否全部还款
 * @property string $refundedAt 回款时间 dateTime类型 => 本期全部还款结束时间
 */
class OfflineRepayment extends ActiveRecord
{

    public function rules()
    {
        return [
            [['loan_id', 'term', 'dueDate'], 'required'],
            [['loan_id', 'term', 'isRepaid', 'isRefunded'], 'integer'],
            [['dueDate', 'repaidAt', 'refundedAt'], 'safe'],
            [['amount', 'principal', 'interest'], 'number']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => '标的ID',
            'term' => '分期期数',
            'dueDate' => '预期还款时间',
            'amount' => '应还总金额',
            'principal' => '本金',
            'interest' => '利息',
            'isRepaid' => '是否还款',
            'repaidAt' => '还款时间',
            'isRefunded' => '是否回款',
            'refundedAt' => '回款时间',
        ];
    }

    public function getLoan()
    {
        return $this->hasOne(OfflineLoan::className(), ['id' => 'loan_id']);
    }
    public function getPlans()
    {
        return OfflineRepaymentPlan::find()->where(['loan_id' => $this->loan_id, 'qishu' => $this->term])->all();
    }
    //当前期所有订单是否全部还完
    public function getIsAllRefunded()
    {
        $plans = OfflineRepaymentPlan::find()->where([
            'loan_id' => $this->loan_id,
            'qishu' => $this->term,
            'status' => 0,
            ])->all();
        if (count($plans) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
