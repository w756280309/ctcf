<?php

namespace app\modules\credit\controllers;

use common\controllers\HelpersTrait;
use common\lib\credit\CreditNote;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class OrderController extends Controller
{
    use HelpersTrait;

    public function behaviors()
    {
        return [
            'access' => [    //登录控制,如果没有登录,则跳转到登录页面
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
     * 转让购买页
     */
    public function actionOrder()
    {
        $note_id = \Yii::$app->request->get('note_id');
        if (empty($note_id)) {
            throw $this->ex404();
        }

        $respData = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => (int) $note_id,  'is_long' => true], function(\Exception $e) {
            $code = $e->getCode();

            if (200 !== $code) {
                throw $this->ex404();
            }
        });

        $loan = $this->findOr404(OnlineProduct::class, $respData['asset']['loan_id']);
        $order = $this->findOr404(OnlineOrder::class, $respData['asset']['order_id']);
        $user = $this->getAuthedUser();

        return $this->render('order', ['loan' => $loan, 'order' => $order, 'user' => $user, 'respData' => $respData]);
    }

    /**
     * 新建订单
     */
    public function actionNew()
    {
        $request = \Yii::$app->request;
        $user = $this->getAuthedUser();
        $noteId = $request->post('note_id');
        $principal = $request->post('amount');//实际购买本金

        $creditNote = new CreditNote();
        $checkResult = $creditNote->check($noteId, $principal, $user);
        if (1 === $checkResult['code']) {
            $checkResult['url'] = '';
            return $checkResult;
        }
        try {
            $txClient = \Yii::$container->get('txClient');

            $res = $txClient->post('credit-order/new', [
                'user_id' => $user->getId(),
                'note_id' => $noteId,
                'principal' => bcmul($principal, 100, 0),
            ]);

            if (isset($res['id']) && $res['id'] > 0) {
                return ['code' => 0, 'url' => '/credit/order/wait?order_id=' . $res['id']];
            } else {
                return ['code' => 1, 'url' => '/credit/order/refer'];
            }
        } catch (\Exception $ex) {
            return ['code' => 1, 'url' => '', 'message' => $ex->getMessage()];
        }
    }

    /**
     * 确认等待页
     */
    public function actionWait()
    {
        $order_id = \Yii::$app->request->get('order_id');
        if (empty($order_id)) {
            throw $this->ex404('订单不存在');
        }
        $txClient = \Yii::$container->get('txClient');
        $order = $txClient->get('credit-order/detail', ['id' => $order_id]);
        if (null === $order) {
            throw $this->ex404('订单不存在');
        }
        return $this->render('wait', ['order_id' => $order_id]);
    }

    /**
     * 结果提示页
     */
    public function actionRefer()
    {
        $request = \Yii::$app->request;
        $order_id = intval($request->get('order_id'));
        if (empty($order_id)) {
            return $this->render('refer', ['ret' => 'fail']);
        }

        $txClient = \Yii::$container->get('txClient');
        $order = $txClient->get('credit-order/detail', ['id' => $order_id]);
        $status = (null !== $order && $order['status'] === 1) ? 0 : 1;
        if ($request->isAjax) {
            return ['status' => $status];
        } else {
            return $this->render('refer', [
                'order' => $order,
                'ret' => $status === 0 ? 'success' : 'fail',
            ]);
        }
    }
}
