<?php

namespace backend\modules\product\models;

use common\models\order\OnlineOrder;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use yii\base\Model;

/**
 * @property string $title
 * @property int $status
 * @property bool $isTest
 * @property bool $isHide
 * @property string $internalTitle
 * @property string $sn
 * @property string $issuerSn
 * @property bool   $isXs
 * @property string $finishDateStart
 * @property string $finishDateEnd
 * @property string $investDateStart
 * @property string $investDateEnd
 * @property int    $days
 */
class LoanSearch extends Model
{
    public $title;
    public $status;
    public $isTest;
    public $isHide;
    public $internalTitle;
    public $sn;
    public $issuerSn;
    public $isXs;
    public $finishDateStart;
    public $finishDateEnd;
    public $investDateStart;
    public $investDateEnd;
    public $days;
    public $cid;
    public $originalBorrower;
    public $original_borrower_id;
    public $hasInnerJoinOrder = false;

    public function rules()
    {
        return [
            [['status', 'days', 'cid', 'original_borrower_id'], 'filter', 'filter' => function ($value) {
                if (!is_null($value) && $value !== '') {
                    return intval($value);
                } else {
                    return null;
                }
            }],
            [['title', 'internalTitle', 'sn', 'issuerSn', 'finishDateStart', 'finishDateEnd', 'investDateStart', 'investDateEnd', 'originalBorrower'], 'filter', 'filter' => function ($value) {
                if (!is_null($value) && $value !== '') {
                    return trim($value);
                } else {
                    return null;
                }
            }],
            [['isTest', 'isHide', 'isXs'], 'filter', 'filter' => function ($value) {
                if (!is_null($value) && $value !== '') {
                    return boolval($value);
                } else {
                    return null;
                }
            }],
        ];
    }


    public function search()
    {
        $this->validate();
        $loanTable = OnlineProduct::tableName();
        $orderTable = OnlineOrder::tableName();

        $query = OnlineProduct::find();

        //标的sn
        $query->andFilterWhere(['like', "$loanTable.sn", $this->sn]);

        //项目名称
        $query->andFilterWhere(['like', "$loanTable.title", $this->title]);

        //底层融资方
        if ($this->original_borrower_id) {
            $query->andWhere("find_in_set($this->original_borrower_id,$loanTable.original_borrower_id)");
        }
        //项目副标题
        $query->andFilterWhere(['like', "$loanTable.internalTitle", $this->internalTitle]);

        //状态选择
        if (-3 === $this->status) {
            $query->andWhere(["$loanTable.online_status" => 0, "$loanTable.check_status" => 0]);
        } elseif (-2 === $this->status) {
            $query->andWhere(["$loanTable.online_status" => 0, "$loanTable.check_status" => 1]);
        } elseif (-1 === $this->status) {
            $query->andWhere(["$loanTable.online_status" => 0, "$loanTable.check_status" => 2]);
        } elseif (0 === $this->status) {
            $query->andWhere(["$loanTable.online_status" => 0, "$loanTable.check_status" => 3]);
        } elseif ($this->status) {
            $query->andWhere(["$loanTable.online_status" => OnlineProduct::STATUS_ONLINE, "$loanTable.status" => $this->status]);
        }

        //标的显示及隐藏列表切换
        if ($this->isHide) {
            $query->andWhere([
                "$loanTable.del_status" => OnlineProduct::STATUS_DEL,
                "$loanTable.status" => OnlineProduct::STATUS_FOUND,
            ]);
        } else {
            $query->andWhere(["$loanTable.del_status" => OnlineProduct::STATUS_USE]);
        }

        //根据是否测试标进行过滤
        $query->andWhere(["$loanTable.isTest" => $this->isTest]);

        //根据发行方编号筛选标的
        $query->andFilterWhere(["$loanTable.issuerSn" => $this->issuerSn]);

        //新手标
        $query->andFilterWhere(["$loanTable.is_xs" => $this->isXs]);

        //到期日
        if ($this->finishDateStart) {
            $query->andWhere([">=", "$loanTable.finish_date" , (new \DateTime($this->finishDateStart . ' 00:00:00'))->getTimestamp()]);
        }
        if ($this->finishDateEnd) {
            $query->andWhere(["<=", "$loanTable.finish_date" , (new \DateTime($this->finishDateEnd . ' 23:59:59'))->getTimestamp()]);
        }

        //根据标的类型是温盈金还是温盈宝进行筛选1,温盈金   2,温盈宝
        $query->andFilterWhere(["$loanTable.cid" => $this->cid]);

        //根据投资时间筛选标的
        if ($this->investDateStart) {
            $query->innerJoin("$orderTable", "$orderTable.online_pid = $loanTable.id");
            $this->hasInnerJoinOrder = true;
            $query->andWhere([">=", "$orderTable.order_time", (new \DateTime($this->investDateStart . ' 00:00:00'))->getTimestamp()]);
        }
        if ($this->investDateEnd) {
            if (!$this->hasInnerJoinOrder) {
                $query->innerJoin("$orderTable", "$orderTable.online_pid = $loanTable.id");
                $this->hasInnerJoinOrder = true;
            }
            $query->andWhere(["<=", "$orderTable.order_time", (new \DateTime($this->investDateEnd . ' 23:59:59'))->getTimestamp()]);
        }
        if ($this->hasInnerJoinOrder) {
            $query->groupBy("$loanTable.id");
        }
        //最近 $days 天待还款
        if ($this->days > 0) {
            $endDay = date('Y-m-d', strtotime("+{$this->days} days"));    //所有区段都要统计自截止日之前的所有待还款项目
            $repaymentTable = Repayment::tableName();
            $loanIds = Repayment::find()
                ->innerJoin(["$loanTable", "$repaymentTable.loan_id = $loanTable.id"])
                ->where(['<', "$repaymentTable.dueDate", $endDay])
                ->andWhere([
                    "$repaymentTable.isRefunded" => 0,
                    "$loanTable.status" => OnlineProduct::STATUS_HUAN,
                    "$loanTable.isTest" => 0
                ])
                ->select("$repaymentTable.loan_id")
                ->distinct()
                ->column();
            $query->andWhere(['in', "$loanTable.id", $loanIds]);
        }

        return $query;
    }
}
