<?php

namespace frontend\modules\credit\controllers;

use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\order\OnlineOrder as Order;
use common\models\product\OnlineProduct as Loan;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class TradeController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [ //登录控制,如果没有登录,则跳转到登录页面
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

    public function actions()
    {
        return [
            'cancel' => '\common\action\credit\CancelAction',
        ];
    }

    /**
     * 债权转让列表.
     */
    public function actionAssets($type = 1, $page = 1)
    {
        $type = intval($type);

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        $user = $this->getAuthedUser();

        $data = [];
        $pageSize = 10;

        if (1 === $type) {   //可转让列表
            $respData = Yii::$container->get('txClient')->get('assets/transferable-list', [
                'user_id' => $user->id,
                'offset' => ($page - 1) * $pageSize,
                'limit' => $pageSize,
            ]);

            $assets = $respData['data'];
            $pages = new Pagination(['totalCount' => $respData['totalCount'], 'pageSize' => $pageSize]);

            foreach ($assets as $key => $asset) {
                $cond = ['online_pid' => $asset['loan_id'], 'uid' => $asset['user_id']];
                if (!empty($asset['note_id'])) {
                    $cond['asset_id'] = $asset['id'];
                } else {
                    $cond['order_id'] = $asset['order_id'];
                    $cond['asset_id'] = null;
                }

                $assets[$key]['loan'] = Loan::findOne($asset['loan_id']);
                $assets[$key]['order'] = Order::findOne($asset['order_id']);
                $assets[$key]['plan'] = Plan::find()
                    ->where($cond)
                    ->asArray()
                    ->all();
            }

            $data = [
                'assets' => $assets,
                'creditAmount' => $respData['creditAmount'],
                'totalCount' => $respData['totalCount'],
                'type' => $type,
                'pages' => $pages,
            ];
        } else {    //转让中列表
            $stats = Yii::$container->get('txClient')->get('credit-note/user-notes-stats', [
                'user_id' => $user->id,
                'type' => $type,
            ]);

            $pages = new Pagination(['totalCount' => $stats['totalCount'], 'pageSize' => $pageSize]);

            $notes = Yii::$container->get('txClient')->get('credit-note/user-notes', [
                'user_id' => $user->id,
                'type' => $type,
                'offset' => $pages->offset,
                'limit' => $pages->limit,
            ]);

            $loans = Loan::findAll(['id' => array_column($notes['data'], 'loan_id')]);
            $loans = ArrayHelper::index($loans, 'id');

            $data = [
                'notes' => $notes['data'],
                'tradedTotalAmount' => $stats['tradedTotalAmount'],
                'totalCount' => $stats['totalCount'],
                'type' => $type,
                'pages' => $pages,
                'loans' => $loans,
            ];

            if (2 === $type) {
                $data['tradingTotalAmount'] = $stats['tradingTotalAmount'];
            } else {
                $actualIncome = [];
                if (!empty($notes['data'])) {
                    $ids = implode(',', array_column($notes['data'], 'id'));

                    $actualIncome = Yii::$container->get('txClient')->get('credit-note/actual-income', [
                        'ids' => $ids,
                    ]);
                }

                $data['actualIncome'] = $actualIncome;
            }
        }

        return $this->render('assets', $data);
    }
}
