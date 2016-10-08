<?php

namespace app\modules\credit\controllers;

use common\controllers\HelpersTrait;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\BankService;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class NoteController extends Controller
{
    use HelpersTrait;

    public function actions()
    {
        return [
            'new' => ['class' => 'common\action\credit\NewAction'],
            'create' => ['class' => 'common\action\credit\CreateAction'],
        ];
    }

    /**
     * 转让详情页.
     */
    public function actionDetail($id)
    {
        $user = $this->getAuthedUser();

        if (empty($id)
            || (!Yii::$app->params['feature_credit_note_on']        //当债权功能开关关闭的时候,如果访问用户不是白名单里面的用户ID,就抛404异常
            && !in_array($user->id, Yii::$app->params['feature_credit_note_whitelist_uids']))) {
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

        return $this->render('detail', ['loan' => $loan, 'order' => $order, 'user' => $user, 'respData' => $respData]);
    }

    /**
     * 转让记录.
     *
     * 1.一页显示十条记录;
     */
    public function actionOrders($id, $page = 1)
    {
        $pageSize = 10;

        $respData = Yii::$container->get('txClient')->get('credit-order/list', [
            'id' => $id,
            'page' => $page,
            'page_size' => $pageSize,
        ], function(\Exception $e) {
            $code = $e->getCode();

            if (200 !== $code) {
                throw $this->ex404();
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
        } else {
            $users = [];
        }

        $pages = new Pagination([
            'totalCount' => $respData['totalCount'],
            'pageSize' => $respData['pageSize'],
        ]);
        $tp = $pages->pageCount;
        $header = [
            'count' => $respData['totalCount'],
            'size' => $respData['pageSize'],
            'tp' => $tp,
            'cp' => $respData['page'],
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/credit/views/note/_more_order.php', ['orders' => $orders, 'users' => $users]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('orders', ['orders' => $orders, 'users' => $users, 'id' => $id, 'pages' => $pages]);
    }

    /**
     * 债权转让规则.
     */
    public function actionRules()
    {
        return $this->render('rules');
    }

    /**
     * 进入购买页面前添加开户，免密，快捷判断
     */
    public function actionCheck()
    {
        //检查是否登录
        $user = $this->getAuthedUser();
        if (null === $user) {
            return ['tourl' => '/site/login', 'code' => 1, 'message' => '请登录'];
        }

        if ($user->status == 0) {
            return ['tourl' => '/site/usererror', 'code' => 1, 'message' => '账户已被冻结'];
        }

        //检查是否开通资金托管与免密
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE | BankService::BINDBANK_VALIDATE_N;
        $checkResult = BankService::check($user, $cond);

        return $checkResult;
    }
}
