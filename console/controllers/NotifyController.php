<?php

namespace console\controllers;


use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use Ding\DingNotify;
use yii\console\Controller;

class NotifyController extends Controller
{
    public function actionRepaymentNotify()
    {
        $op = OnlineProduct::tableName();
        $r = Repayment::tableName();
        $query = Repayment::find()->innerJoin($op, "$r.loan_id = $op.id");
        $new_query = clone $query;
        //查询当天
        $query->where(['<', 'dueDate', date('Y-m-d', strtotime("+1 days"))]);//所有区段都要统计自截止日之前的所有待还款项目
        $res1 = $query->andWhere(['isRefunded' => 0, "$op.status" => OnlineProduct::STATUS_HUAN])->select(['loan_id'])->distinct()->count();
        //查询7天
        $new_query->where(['<', 'dueDate', date('Y-m-d', strtotime("+7 days"))]);//所有区段都要统计自截止日之前的所有待还款项目
        $res2 = $new_query->andWhere(['isRefunded' => 0, "$op.status" => OnlineProduct::STATUS_HUAN, "$op.isTest" => 0])->select(['loan_id'])->distinct()->count();

        $notify = new DingNotify();
        $notify->sendMessage('7天内有 【' . $res2 . '】 个项目等待还款；当天有 【' . $res1 . '】 个项目等待还款！');
    }
}