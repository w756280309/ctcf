<?php

namespace frontend\modules\user\controllers;

use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder as Ord;
use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\product\OnlineProduct as Loan;
use common\models\user\MoneyRecord;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
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
            } elseif (in_array($val->type, [MoneyRecord::TYPE_CREDIT_NOTE, MoneyRecord::TYPE_CREDIT_NOTE_FEE, MoneyRecord::TYPE_CREDIT_REPAID])) {
                $creditOrder = Yii::$container->get('txClient')->get('credit-order/detail', [
                    'id' => $val->osn,
                ]);

                $creditNode = Yii::$container->get('txClient')->get('credit-note/detail', [
                    'id' => $creditOrder['note_id'],
                ]);

                $desc[$key]['nodeId'] = $creditNode['id'];
                $desc[$key]['loan'] = Loan::findOne($creditNode['loan_id']);
            } elseif (MoneyRecord::TYPE_CREDIT_HUIKUAN === $val->type) {
                $asset = Yii::$container->get('txClient')->get('assets/detail', [
                    'id' => $val->osn,
                ]);

                $desc[$key]['nodeId'] = $asset['note_id'];
                $desc[$key]['loan'] = Loan::findOne($asset['loan_id']);
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
            ->where(["$o.uid" => $user->id, "$l.status" => [2, 3, 7], "$o.status" => Ord::STATUS_SUCCESS])
            ->orderBy(["$o.id" => SORT_DESC])
            ->limit(5)
            ->all();

        $assets = Yii::$container->get('txClient')->get('assets/list', [
            'user_id' => $user->id,
            'type' => 1,
            'page_size' => 5,
        ]);

        //将债权订单记录按照创建时间的由近到远的顺序逐条插入到普通订单信息当中
        foreach ($assets['data'] as $asset) {
            //排除掉已经计息但未到收益中标的资产，否则会造成记录显示重复
            if (in_array($asset['loan']['status'], [3, 7])) {
                continue;
            }
            $insertFlag = false;    //判断是否执行了插入操作标志位
            foreach ($orders as $key => $order) {
                $assetTime = empty($asset['note_id']) ? $asset['orderTime'] : $asset['createTime'];
                $_createTime = isset($order['createTime'])
                    ? (empty($order['note_id']) ? $order['orderTime'] : $order['createTime'])
                    : date('Y-m-d H:i:s', $order['created_at']);    //如果是普通订单,就使用订单创建时间来判断;如果是债权,且没有被转让过就使用订单时间来判断,否则使用创建时间来判断

                if ($assetTime <= $_createTime) {   //寻找插入的位置
                    continue;
                }

                $data = array_splice($orders, $key);    //找到位置后,插入记录
                $orders[$key] = $asset;
                array_splice($orders, $key + 1, 1, $data);
                $insertFlag = true;
                break;
            }

            if (!$insertFlag) { //如果前面没有执行插入操作,表明应该将该条记录插入到数组的末尾
                array_push($orders, $asset);
            }
        }

        foreach ($orders as $key => $order) {
            if ($key >= 5) {
                break;
            }

            if (isset($order['createTime'])) {
                $cond = ['online_pid' => $order['loan_id'], 'uid' => $user->id];
                if (!empty($order['note_id'])) {
                    $cond['asset_id'] = $order['id'];
                } else {
                    $cond['order_id'] = $order['order_id'];
                }

                $data = Plan::findAll($cond);
                $orders[$key]['shouyi'] = array_sum(ArrayHelper::getColumn($data, 'lixi'));
            }
        }

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
                ->andWhere(["$l.is_jixi" => false])
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
                $cond = ['online_pid' => $val['loan_id'], 'uid' => $user->id];
                if (!empty($val['note_id'])) {
                    $cond['asset_id'] = $val['id'];
                } else {
                    $cond['order_id'] = $val['order_id'];
                    $cond['asset_id'] = null;
                }

                $data = Plan::findAll($cond);
                $model[$key]['order'] = Ord::findOne($val['order_id']);
                $model[$key]['shouyi'] = array_sum(ArrayHelper::getColumn($data, 'lixi'));
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

    /**
     * @param int $page
     * @param int $type ['1' => '收益中', '2' => '募集中', '3' => '已还清']
     * @return mixed
     */
    public function actionMyofforder($type = 1)
    {
        if (!in_array($type, ['1', '3', '2'])) {
            throw $this->ex404();
        }
        $type = intval($type);
        $user = $this->getAuthedUser();
        $sql = "select max(id) from offline_repayment_plan where uid = :uid GROUP BY loan_id";
        $plan = Yii::$app->db->createCommand($sql, ['uid' => $user->offlineUserId])->queryColumn();
        $query = OfflineOrder::find()
            ->select('offline_order.*')
            ->innerJoin('offline_loan', 'offline_order.loan_id = offline_loan.id')
            ->where([
                'offline_order.user_id' => $user->offlineUserId,
                'offline_order.isDeleted' => false,
                ]);
        if ($type == 1) {
            $query->innerJoin('offline_repayment_plan', 'offline_repayment_plan.loan_id = offline_loan.id')
                ->andWhere([
                    'offline_loan.is_jixi' => true,
                    'offline_repayment_plan.id' => $plan,
                    'offline_repayment_plan.status' => 0,
                    ])
                ->orderBy(['offline_order.orderDate' => SORT_ASC]);
        } else if ($type == 3) {
            $query->innerJoin('offline_repayment_plan', 'offline_repayment_plan.loan_id = offline_loan.id')
                ->andWhere([
                    'offline_loan.is_jixi' => true,
                    'offline_repayment_plan.id' => $plan,
                    'offline_repayment_plan.status' => [1, 2],
                    ])
                ->orderBy(['offline_order.orderDate' => SORT_ASC]);
        } else if ($type == 2){
            $query->andWhere(['offline_loan.is_jixi' => false]);
        }
        $query->orderBy("created_at desc");
        $count = $query->count();
        $pageSize = 10;
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $models = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('myofforder', [
            'models' => $models,
            'pages' => $pages,
            'count' => $count,
            'type' => $type,
            ]);
    }
}