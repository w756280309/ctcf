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
    public function actionMyorder($type = 1, $page = 1)
    {
        $type = intval($type);
        $pageSize = 10;

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        $user = $this->getAuthedUser();
        $o = Ord::tableName();
        $l = Loan::tableName();
        $p = Plan::tableName();

        switch ($type) {
            case 1:
            case 3:
                $stats = Yii::$container->get('txClient')->get('assets/plan-stats', [
                    'user_id' => $user->id,
                    'type' => $type,
                ]);
                break;
            case 2:
                $status = [Loan::STATUS_NOW, Loan::STATUS_FULL, Loan::STATUS_FOUND];
        }

        if (2 === $type) {
            $query = Ord::find()
                ->innerJoinWith('loan')
                ->where(["$o.uid" => $user->id, "$o.status" => Ord::STATUS_SUCCESS])
                ->andWhere(["$l.status" => $status])
                ->orderBy("$o.id desc");

            $count = $query->count();
            $tj['count'] = $count;
            $tj['totalAmount'] = $query->sum('order_money');

            $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
            $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        } else {
            $assets = Yii::$container->get('txClient')->get('assets/list', [
                'user_id' => $user->id,
                'type' => $type,
                'page' => $page,
                'page_size' => $pageSize,
            ]);

            $tj['count'] = $assets['totalCount'];

            $model = $assets['data'];
            $pages = new Pagination(['totalCount' => $assets['totalCount'], 'pageSize' => $pageSize]);
        }

        $plan = [];

        foreach ($model as $key => $val) {
            if (2 === $type) {
                $data = Plan::findAll(['online_pid' => $val->online_pid, 'uid' => $user->id, 'order_id' => $val->id]);
            } else {
                $data = Plan::findAll(['online_pid' => $val['loan_id'], 'uid' => $user->id, 'order_id' => $val['order_id']]);
                $model[$key]['order'] = Ord::findOne($val['order_id']);
            }

            $plan[$key]['obj'] = $data;
            $plan[$key]['yihuan'] = 0;

            foreach ($data as $v) {
                if (in_array($v->status, [Plan::STATUS_YIHUAN, Plan::STATUS_TIQIAM])) {
                    ++$plan[$key]['yihuan'];
                }
            }
        }

        return $this->render('myorder', [
            'model' => $model,
            'pages' => $pages,
            'type' => $type,
            'plan' => $plan,
            'tj' => $tj,
            'stats' => $stats,
        ]);
    }
}