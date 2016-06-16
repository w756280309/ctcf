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
            ->where(['uid' => Yii::$app->user->identity->id])
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
        $o = Ord::tableName();
        $l = Loan::tableName();
        $orders = Ord::find()
            ->innerJoinWith('loan')
            ->where(["$o.uid" => $this->user->id, "$l.status" => [2, 3, 5, 7]])
            ->orderBy(["$o.id" => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'orders' => $orders,
            'user' => $this->user,
        ]);
    }
}
