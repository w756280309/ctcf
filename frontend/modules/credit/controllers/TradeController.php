<?php

namespace frontend\modules\credit\controllers;

use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\order\OnlineOrder as Order;
use common\models\product\OnlineProduct as Loan;
use frontend\controllers\BaseController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
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

    /**
     * 债权转让列表.
     */
    public function actionAssets($type = 1)
    {
        $type = intval($type);

        if (!in_array($type, [1, 2, 3])) {
            throw $this->ex404();
        }

        $user = $this->getAuthedUser();

        $data = [];
        $pageSize = 10;

        if (1 === $type) {   //可转让列表
            $assets = Yii::$container->get('txClient')->get('assets/transferable-list', [
                'user_id' => $user->id,
            ]);

            if (empty($assets)) {
                $assets = [];
                $creditAmount = 0;
                $totalCount = 0;
            } else {
                $creditAmount = array_sum(array_column($assets, 'maxTradableAmount'));
                $totalCount = count($assets);

                $provider = new ArrayDataProvider([
                    'allModels' => $assets,
                    'pagination' => [
                        'pageSize' => $pageSize,
                    ],
                ]);

                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
                $assets = $provider->getModels();

                foreach ($assets as $key => $asset) {
                    $assets[$key]['loan'] = Loan::findOne($asset['loan_id']);
                    $assets[$key]['order'] = Order::findOne($asset['order_id']);
                    $assets[$key]['plan'] = Plan::find()
                        ->where(['online_pid' => $asset['loan_id'], 'uid' => $asset['user_id'], 'order_id' => $asset['order_id']])
                        ->asArray()
                        ->all();
                }
            }

            $data = [
                'assets' => $assets,
                'creditAmount' => $creditAmount,
                'totalCount' => $totalCount,
                'type' => $type,
                'pages' => $pages,
            ];
        } elseif (2 === $type) {    //转让中列表
            $notes = Yii::$container->get('txClient')->get('credit-note/user-notes', [
                'user_id' => $user->id,
            ]);

            if (empty($notes)) {
                $notes = [];
                $tradedTotalAmount = 0;
                $tradingTotalAmount = 0;
                $totalCount = 0;
            } else {
                foreach ($notes as $key => $note) {
                    if ($note['isClosed']) {
                        unset($notes[$key]);
                        continue;
                    } else {
                        $notes[$key]['loan'] = Loan::findOne($note['loan_id']);
                    }
                }

                $tradedTotalAmount = array_sum(array_column($notes, 'tradedAmount'));
                $tradingTotalAmount = array_sum(array_column($notes, 'amount')) - $tradedTotalAmount;
                $totalCount = count($notes);

                $provider = new ArrayDataProvider([
                    'allModels' => $notes,
                    'pagination' => [
                        'pageSize' => $pageSize,
                    ],
                ]);

                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
                $notes = $provider->getModels();
            }

            $data = [
                'notes' => $notes,
                'tradedTotalAmount' => $tradedTotalAmount,
                'tradingTotalAmount' => $tradingTotalAmount,
                'totalCount' => $totalCount,
                'type' => $type,
                'pages' => $pages,
            ];
        } elseif (3 === $type) {    //已转让列表
            $notes = Yii::$container->get('txClient')->get('credit-note/user-notes', [
                'user_id' => $user->id,
            ]);

            if (empty($notes)) {
                $notes = [];
                $tradedTotalAmount = 0;
                $totalCount = 0;
            } else {
                foreach ($notes as $key => $note) {
                    if ($note['isClosed'] && $note['tradedAmount'] > 0) {
                        $notes[$key]['loan'] = Loan::findOne($note['loan_id']);
                        continue;
                    } else {
                        unset($notes[$key]);
                    }
                }

                $tradedTotalAmount = array_sum(array_column($notes, 'tradedAmount'));
                $totalCount = count($notes);

                $provider = new ArrayDataProvider([
                    'allModels' => $notes,
                    'pagination' => [
                        'pageSize' => $pageSize,
                    ],
                ]);

                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
                $notes = $provider->getModels();
            }

            $data = [
                'notes' => $notes,
                'tradedTotalAmount' => $tradedTotalAmount,
                'totalCount' => $totalCount,
                'type' => $type,
                'pages' => $pages,
            ];
        }

        return $this->render('assets', $data);
    }

    /**
     * 撤销指定id的挂牌记录
     *
     * @param int    $id
     *
     * @return array ['code' => '0 or 1', 'message' =>'']
     */
    public function actionCancel($id)
    {
        if (empty($id) || null === ($note = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => $id]))) {
            return ['code' => 1, 'message' => '没有找到该转让信息'];
        }

        $userId = $this->getAuthedUser()->getId();
        if ((int) $userId !== $note['user_id']) {
            return ['code' => 1, 'message' => '非本人不能撤销该转让信息'];
        }

        try {
            Yii::$container->get('txClient')->get('credit-note/cancel', [
                'id' => $id,
            ]);
        } catch (\Exception $ex) {
            $result = json_decode(strval($ex->getResponse()->getBody()), true);
            if (isset($result['name'])
                && $result['name'] === 'Bad Request'
                && isset($result['message'])
                && isset($result['status'])
                && $result['status'] !== 200
            ) {
                return ['code' => 1, 'message' => $result['message']];
            }
        }

        return ['code' => 0, 'message' => '撤销成功'];
    }
}
