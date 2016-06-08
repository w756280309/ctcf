<?php

namespace frontend\modules\user\controllers;

use common\models\order\OnlineOrder as Ord;
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

    public function actionMingxi()
    {
        $query = MoneyRecord::find()->select(['created_at', 'type', 'in_money', 'out_money', 'balance', 'osn'])->where(['uid' => Yii::$app->user->identity->id])->andWhere(['in', 'type', MoneyRecord::getLenderMrType()]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 10]);
        $query = $query->orderBy(['id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit);
        $lists = $query->all();

        return $this->render('mingxi', [
            'pages' => $pages,
            'lists' => $lists,
        ]);
    }

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
