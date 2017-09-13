<?php

namespace backend\modules\product\models;

use common\models\order\OnlineOrder;
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

    public $hasInnerJoinOrder = false;

    public function rules()
    {
        return [
            [['status'], 'filter', 'filter' => function ($value) {
                if (!is_null($value) && $value !== '') {
                    return intval($value);
                } else {
                    return null;
                }
            }],
            [['title', 'internalTitle', 'sn', 'issuerSn', 'finishDateStart', 'finishDateEnd', 'investDateStart', 'investDateEnd'], 'filter', 'filter' => function ($value) {
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

        //项目副标题
        $query->andFilterWhere(['like', "$loanTable.internalTitle", $this->internalTitle]);

        //状态选择
        if (0 === $this->status) {
            $query->andWhere(["$loanTable.online_status" => $this->status]);
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

        return $query;
    }
}
