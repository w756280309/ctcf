<?php

namespace frontend\modules\credit\controllers;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use common\service\BankService;

class NoteController extends BaseController
{
    //发起债权页面
    public function actionNew($asset_id)
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }
        //获取资产详情
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id]);
        if (null === $asset) {
            throw $this->ex404('没有找到指定资产');
        }
        $loan = OnlineProduct::findOne($asset['loan_id']);
        $order = OnlineOrder::findOne($asset['order_id']);
        $apr = $order->yield_rate;

        return $this->render('new', [
            'asset' => $asset,
            'loan' => $loan,
            'apr' => $apr,
        ]);
    }

    //ajax请求发起挂牌记录
    public function actionCreate()
    {
        $asset_id = \Yii::$app->request->post('asset_id');
        $amount = \Yii::$app->request->post('amount');
        $rate = \Yii::$app->request->post('rate', 0);
        $rate = $rate ?: 0;
        if ($asset_id > 0 && $amount > 0 && $rate >= 0) {
            try {
                $txClient = \Yii::$container->get('txClient');
                $result = $txClient->post('credit-note/new', [
                    'discountRate' => $rate,
                    'asset_id' => $asset_id,
                    'amount' => $amount * 100,
                ]);
                $responseData = ['code' => 0, 'data' => $result];
            } catch (\Exception $e) {
                $result = json_decode(strval($e->getResponse()->getBody()), true);
                if (isset($result['name'])
                    && $result['name'] === 'Bad Request'
                    && isset($result['message'])
                    && isset($result['status'])
                    && $result['status'] !== 200
                ) {
                    //获取没有指定属性的错误信息
                    $responseData = ['code' => 1, 'data' => [['msg' => $result['message'], 'attribute' => '']]];
                } else {
                    //获取有指定属性的错误信息
                    $data = [];
                    foreach ($result as $attribute => $message) {
                        $data[] = ['attribute' => $attribute, 'msg' => $message];
                    }
                    $responseData = ['code' => 1, 'data' => $data];
                }
            }
        } else {
            $responseData = ['code' => 1, 'data' => [['msg' => '参数错误', 'attribute' => '']]];
        }

        return $responseData;
    }

    /**
     * 转让详情.
     */
    public function actionDetail($id)
    {
        //记录来源
        Yii::$app->session->set('to_url', Yii::$app->request->url);

        if (empty($id)) {
            throw $this->ex404();
        }

        $respData = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => $id], function(\Exception $e) {
            $code = $e->getCode();

            if (200 !== $code) {
                throw $this->ex404();
            }
        });

        $loan = $this->findOr404(OnlineProduct::class, $respData['asset']['loan_id']);
        $order = $this->findOr404(OnlineOrder::class, $respData['asset']['order_id']);
        $user = $this->getAuthedUser();

        return $this->render('detail', ['loan' => $loan, 'order' => $order, 'user' => $user, 'respData' => $respData]);
    }

    /**
     * 获取转让订单信息.
     */
    public function actionOrders($id, $page = null)
    {
        $pageSize = 10;

        if (empty($page)) {
            $page = 1;
        }


        $respData = Yii::$container->get('txClient')->get('credit-note/orders', [
            'id' => $id,
            'page' => $page,
            'page_size' => $pageSize,
        ], function(\Exception $e) {
            $code = $e->getCode();

            if (200 !== $code) {
                return ['data' => []];
            }
        });

        $orders = $respData['data'];

        if (!empty($orders)) {
            $users = User::find()
                ->where(['id' => ArrayHelper::getColumn($orders, 'user_id')])
                ->asArray()
                ->all();

            if (!empty($users)) {
                $users = ArrayHelper::index($users, 'id');
            }

            $pages = new Pagination([
                'totalCount' => $respData['totalCount'],
                'pageSize' => $respData['pageSize'],
            ]);
        } else {
            $users = null;
        }

        $this->layout = false;
        return $this->render('_order_list', ['data' => $orders, 'users' => $users, 'pages' => $pages]);
    }

    public function actionCheck($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $amount = \Yii::$app->request->post('amount');

        $user = $this->getAuthedUser();
        if (null === $user) {
            return ['tourl' => '/site/login', 'code' => 1, 'message' => '请登录'];
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE;
        $checkResult = BankService::check($user, $cond);
        if (1 === $checkResult['code']) {
            return $checkResult;
        }

        //判断金额在下一步操作
        return ['tourl' => '/credit/order/confirm?id='.$id.'&amount='.$amount, 'code' => 0, 'message' => ''];
    }
}
