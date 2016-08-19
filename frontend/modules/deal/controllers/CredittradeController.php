<?php

namespace frontend\modules\deal\controllers;

use common\models\product\CreditTrade;
use common\models\product\OnlineProduct;
use frontend\controllers\BaseController;
use yii\data\Pagination;

class CredittradeController extends BaseController
{
    /**
     * 挂牌标的列表.
     * 1. 每页显示10条记录;
     * 2. 排序规则为进行中的在前,已完成的在后,新创建的在前,后创建的在后;.
     */
    public function actionList()
    {
        $c = CreditTrade::tableName();
        $l = OnlineProduct::tableName();

        $query = CreditTrade::find()
            ->innerJoinWith('loan')
            ->innerJoinWith('order')
            ->where(["$l.isPrivate" => 0, "$l.del_status" => OnlineProduct::STATUS_USE, "$l.is_jixi" => 1]);

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $creditTrade = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->select("$c.*, if($c.status = 1, (0), (1)) as orderCode")
            ->orderBy(['orderCode' => SORT_ASC, 'createTime' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($creditTrade as $key => $val) {
            $startDate = new \DateTime($val->createTime);
            $endDate = new \DateTime(date('Y-m-d', $val->loan->finish_date));

            if ($startDate > $endDate) {
                $data[$key]['surplusExpire'] = '0天';
            } else {
                $diff = $startDate->diff($endDate);
                $data[$key]['surplusExpire'] = $diff->m ? $diff->format('%m个月%d天') : $diff->format('%d天');
            }

            $start = new \DateTime();
            $end = new \DateTime($val->endTime);

            if ($start > $end || $val->status !== CreditTrade::STATUS_ONGOING) {
                $data[$key]['surplusTime'] = '0天0时0分';
            } else {
                $data[$key]['surplusTime'] = $start->diff($end)->format('%d天%h时%i分');
            }
        }

        return $this->render('list', ['model' => $creditTrade, 'data' => $data, 'pages' => $pages]);
    }
}
