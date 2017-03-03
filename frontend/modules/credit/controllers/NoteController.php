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
    public function actions()
    {
        return [
            'new' => ['class' => 'common\action\credit\NewAction'],
            'create' => ['class' => 'common\action\credit\CreateAction'],
            'risk-note' => ['class' => 'common\action\credit\RiskNoteAction'],
        ];
    }

    /**
     * 转让详情.
     */
    public function actionDetail($id)
    {
        $user = $this->getAuthedUser();

        if (empty($id)
            || (!Yii::$app->params['feature_credit_note_on']        //当债权功能开关关闭的时候,如果访问用户不是白名单里面的用户ID,就抛404异常
            && !in_array($user->id, Yii::$app->params['feature_credit_note_whitelist_uids']))) {
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

        if ($user->status == 0) {
            return ['tourl' => '/site/usererror', 'code' => 1, 'message' => '账户已被冻结'];
        }

        //检查是否开通资金托管与免密
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE_N;
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
