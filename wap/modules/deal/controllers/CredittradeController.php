<?php

namespace app\modules\deal\controllers;

use common\models\product\CreditTrade;
use common\models\product\OnlineProduct;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;

class CredittradeController extends Controller
{
    /**
     * 挂牌记录列表.
     *
     * 1. 每页显示5条记录;
     * 2. 排序规则为进行中的在前,其他的按创建时间降序排列;
     */
    public function actionList($page = 1)
    {
        $c = CreditTrade::tableName();
        $l = OnlineProduct::tableName();
        $pageSize = 5;

        $query = CreditTrade::find()
            ->innerJoinWith('loan')
            ->innerJoinWith('order')
            ->where(["$l.isPrivate" => 0, "$l.del_status" => OnlineProduct::STATUS_USE, "$l.is_jixi" => 1]);

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $model = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->select("$c.*, if($c.status = 1, (0), (1)) as orderCode")
            ->orderBy(['orderCode' => SORT_ASC, 'createTime' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($model as $key => $val) {
            $startDate = new \DateTime($val->createTime);
            $endDate = new \DateTime(date('Y-m-d', $val->loan->finish_date));

            if ($startDate > $endDate) {
                $data[$key]['surplusExpire'] = '0天';
            } else {
                $diff = $startDate->diff($endDate);
                $data[$key]['surplusExpire'] = $diff->m ? $diff->format('%m个月%d天') : $diff->format('%d天');
            }
        }

        $tp = $pages->pageCount;
        $header = [
            'count' => $count,
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/deal/views/credittrade/_list.php', ['model' => $model, 'data' => $data]);

            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('list', ['model' => $model, 'data' => $data, 'pages' => $pages]);
    }
}
