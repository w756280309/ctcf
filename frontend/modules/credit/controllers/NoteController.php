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
use common\lib\credit\CreditNote;

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
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => true]);
        if (null === $asset) {
            throw $this->ex404('没有找到指定资产');
        }
        if (false === $asset['validate']) {
            throw $this->ex404('债权不合适');
        }
        $loan = OnlineProduct::findOne($asset['loan_id']);
        if (null === $loan || $loan->status !== 5) {
            $this->ex404('没有找到合适标的');
        }
        $order = OnlineOrder::findOne($asset['order_id']);
        if (null === $order) {
            throw $this->ex404('没有找到订单');
        }
        if ($order->uid !== Yii::$app->user->identity->getId()) {
            throw $this->ex404('资产信息不合法');
        }
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
        $amount = floatval(\Yii::$app->request->post('amount'));
        $rate = floatval(\Yii::$app->request->post('rate', 0));
        $rate = $rate ?: 0;
        if ($asset_id > 0 && $amount > 0 && $rate >= 0) {
            try {
                $txClient = \Yii::$container->get('txClient');
                $result = $txClient->post('credit-note/new', [
                    'discountRate' => $rate,
                    'asset_id' => $asset_id,
                    'amount' => bcmul($amount, 100, 0),
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

        $respData = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => $id, 'is_long' => true], function (\Exception $e) {
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


        $respData = Yii::$container->get('txClient')->get('credit-order/list', [
            'id' => $id,
            'page' => $page,
            'page_size' => $pageSize,
        ], function (\Exception $e) {
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

    /**
     * 转让详情-立即投资前金额及用户状态检查
     *
     * @param string $id     挂牌记录id
     * @param string $amount 购买金额（元）
     *
     * @return array
     */
    public function actionCheck()
    {
        $noteId = \Yii::$app->request->get('id');
        $amount = \Yii::$app->request->post('amount');

        //检查是否登录
        $user = $this->getAuthedUser();
        if (null === $user) {
            return ['tourl' => '/site/login', 'code' => 1, 'message' => '请登录'];
        }

        //检查是否开通资金托管与免密
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE;
        $checkResult = BankService::check($user, $cond);
        if (1 === $checkResult['code']) {
            return $checkResult;
        }

        //检查购买金额是否可以购买指定id的转让项目
        $creditNote = new CreditNote();
        $checkNoteResult = $creditNote->check($noteId, $amount, $user);
        if (1 === $checkNoteResult['code']) {
            $checkNoteResult['tourl'] = '';
            return $checkNoteResult;
        }

        return ['tourl' => '/credit/order/confirm?id='.$noteId.'&amount='.$amount, 'code' => 0, 'message' => ''];
    }

    /**
     * 债权转让规则.
     */
    public function actionRules()
    {
        return $this->render('rules');
    }
}
