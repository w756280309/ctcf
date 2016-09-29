<?php

namespace common\models\payment;

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
 * @property integer $isRefunded  是否回款,指给用户转钱
 * @property string $refundedAt 回款时间 dateTime类型
 */
class Repayment extends ActiveRecord
{
    public static function tableName()
    {
        return 'repayment';
    }

    public function rules()
    {
        return [
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
}
