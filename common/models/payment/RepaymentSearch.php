<?php

namespace common\models\payment;


use common\models\product\OnlineProduct;
use yii\db\Query;

class RepaymentSearch extends Repayment
{
    public function attributes()
    {
        return [
            'loanTitle',
            'refundTimeStart',
            'refundTimeEnd',
            'refundMoneyStart',
            'refundMoneyEnd',
            'isRefunded',
        ];
    }

    public function attributeLabels()
    {
        return [
            'loanTitle' => '项目名称',
            'refundTimeStart' => '回款开始时间',
            'refundTimeEnd' => '回款结束时间',
            'refundMoneyStart' => '最小回款金额',
            'refundMoneyEnd' => '最大回款金额',
            'isRefunded' => '回款状态',
        ];
    }


    /**
     * @param $params
     * @return Query
     */
    public function search($params)
    {
        $loanTable = OnlineProduct::tableName();
        $payTable = Repayment::tableName();

        $query = Repayment::find()
            ->innerJoin($loanTable, "$loanTable.id = $payTable.loan_id")
            ->where("$payTable.amount > 0")//测试脏数据
            ->andWhere(["$loanTable.isTest" => false])
        ;
        $this->setAttributes($params, false);

        //标的标题筛选
        if (!empty($this->loanTitle)) {
            $this->loanTitle = trim($this->loanTitle);
            $query->andWhere(['like', "$loanTable.title", $this->loanTitle]);
        }
        //标的状态筛选
        if (!is_null($this->isRefunded) && $this->isRefunded >= 0) {
            $this->isRefunded = boolval($this->isRefunded);
            $query->andWhere(["$payTable.isRefunded" => $this->isRefunded]);
        }

        //还款时间筛选
        $this->refundTimeStart = empty($this->refundTimeStart) ? date('Y-m-01') : trim($this->refundTimeStart);
        $this->refundTimeEnd = empty($this->refundTimeEnd) ? (date('Y-m-') . date('t')) : trim($this->refundTimeEnd);
        if ($this->isRefunded === true) {
            $query->andWhere(['between', "$payTable.refundedAt", $this->refundTimeStart, $this->refundTimeEnd]);
        } elseif($this->isRefunded === false) {
            $query->andWhere(['between', "$payTable.dueDate", $this->refundTimeStart, $this->refundTimeEnd]);
        }


        //还款金额筛选
        if (!empty($this->refundMoneyStart) && $this->refundMoneyStart > 0) {
            $this->refundMoneyStart = floatval(trim($this->refundMoneyStart));
            $query->andWhere(['>=', "$payTable.amount", $this->refundMoneyStart]);
        }
        if (!empty($this->refundMoneyEnd) && $this->refundMoneyEnd > 0) {
            $this->refundMoneyEnd = floatval(trim($this->refundMoneyEnd));
            $query->andWhere(['<=', "$payTable.amount", $this->refundMoneyEnd]);
        }

        $query->orderBy(["$payTable.dueDate" => SORT_ASC]);
        return $query;
    }
}