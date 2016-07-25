<?php

namespace frontend\modules\user\controllers;

use common\models\order\OnlineOrder as Ord;
use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\product\OnlineProduct as Loan;
use common\models\user\MoneyRecord;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class UserController extends BaseController
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 交易明细.
     */
    public function actionMingxi()
    {
        $query = MoneyRecord::find()
            ->where(['uid' => $this->getAuthedUser()->id])
            ->andWhere(['in', 'type', MoneyRecord::getLenderMrType()]);

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 10]);

        $lists = $query
            ->orderBy(['id' => SORT_DESC])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $desc = [];
        foreach ($lists as $key => $val) {
            if ($val->type === MoneyRecord::TYPE_ORDER || $val->type === MoneyRecord::TYPE_HUIKUAN) {
                if ($val->type === MoneyRecord::TYPE_HUIKUAN) {    //回款的时候,流水里面记录的osn是还款计划表中得sn
                    $plan = Plan::findOne(['sn' => $val->osn]);
                    $ord = Ord::findOne($plan->order_id);
                } else {
                    $ord = Ord::findOne(['sn' => $val->osn]);
                }

                if ($ord->loan) {
                    $desc[$key]['desc'] = $ord->loan->title;
                    $desc[$key]['sn'] = $ord->loan->sn;
                } else {
                    $desc[$key]['desc'] = $val->osn;
                }
            } else {
                $desc[$key]['desc'] = $val->osn;
            }
        }

        return $this->render('mingxi', [
            'pages' => $pages,
            'lists' => $lists,
            'desc' => $desc,
        ]);
    }

    /**
     * 账户中心首页
     */
    public function actionIndex()
    {
        //清空session中存储的url信息
        Yii::$app->session->remove('to_url');//记录目的地

        $o = Ord::tableName();
        $l = Loan::tableName();
        $user = $this->getAuthedUser();

        $orders = Ord::find()
            ->innerJoinWith('loan')
            ->where(["$o.uid" => $user->id, "$l.status" => [2, 3, 5, 7], "$o.status" => Ord::STATUS_SUCCESS])
            ->orderBy(["$o.id" => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'orders' => $orders,
            'user' => $user,
        ]);
    }

    /**
     * 我的理财页面.
     * 1. 每页显示10条记录;
     */
    public function actionMyorder($type = 1)
    {
        $type = intval($type);
        if (!in_array($type, [1, 2, 3])) {
            throw $this->ex404();
        }

        $user = $this->getAuthedUser();
        $o = Ord::tableName();
        $l = Loan::tableName();
        $p = Plan::tableName();

        switch ($type) {
            case 1:
                $status = Loan::STATUS_HUAN;
                $tj = Plan::find()
                    ->innerJoin($l, "$l.id=$p.online_pid")
                    ->where(["uid" => $user->id, "$p.status" => Plan::STATUS_WEIHUAN, "$l.status" => 5])
                    ->groupBy("online_pid")
                    ->select("sum(benxi) as benxi")
                    ->asArray()
                    ->all();
                break;
            case 2:
                $status = [Loan::STATUS_NOW, Loan::STATUS_FULL, Loan::STATUS_FOUND];
                break;
            case 3:
                $status = Loan::STATUS_OVER;
                $tj = Plan::find()
                    ->innerJoin($l, "$l.id=$p.online_pid")
                    ->where(["uid" => $user->id, "$p.status" => Plan::STATUS_YIHUAN, "$l.status" => 6])
                    ->groupBy("online_pid")
                    ->select("sum(benxi) as benxi")
                    ->asArray()
                    ->all();
                break;
        }

        $query = Ord::find()
            ->innerJoinWith('loan')
            ->where(["$o.uid" => $user->id, "$o.status" => Ord::STATUS_SUCCESS])
            ->andWhere(["$l.status" => $status])
            ->orderBy("$o.id desc");

        $count = $query->count();
        $tj['count'] = $count;

        if (2 === $type) {
            $tj['totalAmount'] = $query->sum('order_money');
        }

        //计算当前用户收益中项目的本息和，每个项目对应多个订单，每笔订单对应多期还款计划
        $totalbenxi = 0;
        if (1 === $type) {
            $totaldata = $query->all();
            foreach ($totaldata as $v) {
                $totalbenxi += ($v->order_money + Plan::getTotalLixi($v->loan, $v));
            }
        }

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 10]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();

        $plan = [];

        foreach ($model as $key => $val) {
            $data = Plan::findAll(['online_pid' => $val->online_pid, 'uid' => $user->id, 'order_id' => $val->id]);

            $plan[$key]['obj'] = $data;
            $plan[$key]['yihuan'] = 0;

            foreach ($data as $v) {
                if (Plan::STATUS_YIHUAN === $v->status) {
                    ++$plan[$key]['yihuan'];
                }
            }
        }

        return $this->render('myorder', ['model' => $model, 'pages' => $pages, 'type' => $type, 'plan' => $plan, 'tj' => $tj, 'totalbenxi' => $totalbenxi]);
    }
}