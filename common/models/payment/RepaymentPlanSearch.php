<?php

namespace common\models\payment;


use common\models\order\OnlineRepaymentPlan;
use common\models\product\OnlineProduct;
use yii\base\Model;
use yii\db\Query;

class RepaymentPlanSearch extends OnlineRepaymentPlan
{
    public function attributes()
    {
        return [
            'loanTitle',
            'refundTimeStart',
            'refundTimeEnd',
            'loanStatus',
            'refundMoneyStart',
            'refundMoneyEnd',
        ];
    }

    public function attributeLabels()
    {
        return [
            'loanTitle' => '项目名称',
            'refundTimeStart' => '回款开始时间',
            'refundTimeEnd' => '回款结束时间',
            'loanStatus' => '标的状态',
            'refundMoneyStart' => '最小回款金额',
            'refundMoneyEnd' => '最大回款金额',
        ];
    }


    /**
     * @param $params
     * @return Query
     */
    public function search($params)
    {
        $loanTable = OnlineProduct::tableName();
        $planTable = OnlineRepaymentPlan::tableName();

        $query = OnlineRepaymentPlan::find()
            ->innerJoin($loanTable, "$loanTable.id = $planTable.online_pid")
            ->where("$planTable.refund_time > 0")//测试脏数据
        ;
        $this->setAttributes($params, false);

        //标的标题筛选
        if (!empty($this->loanTitle)) {
            $this->loanTitle = trim($this->loanTitle);
            $query->andWhere(['like', "$loanTable.title", $this->loanTitle]);
        }
        //还款时间筛选
        $this->refundTimeStart = empty($this->refundTimeStart) ? date('Y-m-01') : trim($this->refundTimeStart);
        $this->refundTimeEnd = empty($this->refundTimeEnd) ? (date('Y-m-') . date('t')) : trim($this->refundTimeEnd);
        $query->andWhere(['between', "$planTable.refund_time", strtotime($this->refundTimeStart), strtotime($this->refundTimeEnd)]);
        //还款金额筛选
        if (!empty($this->refundMoneyStart) && $this->refundMoneyStart > 0) {
            $this->refundMoneyStart = floatval(trim($this->refundMoneyStart));
            $query->andWhere(['>=', "$planTable.benxi", $this->refundMoneyStart]);
        }
        if (!empty($this->refundMoneyEnd) && $this->refundMoneyEnd > 0) {
            $this->refundMoneyEnd = floatval(trim($this->refundMoneyEnd));
            $query->andWhere(['<=', "$planTable.benxi", $this->refundMoneyEnd]);
        }
        //标的状态筛选
        if (!empty($this->loanStatus)) {
            $this->loanStatus = intval($this->loanStatus);
            $query->andWhere(["$loanTable.status" => $this->loanStatus]);
        } else {
            //测试脏数据
            $query->andWhere(['in', "$loanTable.status", [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER]]);
        }

        $query->groupBy(["$planTable.online_pid", "$planTable.qishu"])->orderBy(["$planTable.refund_time" => SORT_ASC]);
        return $query;
    }
}