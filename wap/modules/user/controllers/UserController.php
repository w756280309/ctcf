<?php

namespace app\modules\user\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\core\UserAccountCore;
use common\models\user\MoneyRecord;
use common\service\OrderService;
use common\service\BankService;

class UserController extends BaseController
{
    /**
     * 账户中心页
     */
    public function actionIndex()
    {
        $user = $this->user;
        $this->layout = 'account';
        $uacore = new UserAccountCore();
        $ua = $this->user->lendAccount;
        $leijishouyi = $uacore->getTotalProfit($user->id);//累计收益
        $dhsbj = $ua->investment_balance;//带回收本金
        $zcze = $uacore->getTotalFund($user->id);//账户余额+理财资产

        $data = BankService::checkKuaijie($user);

        return $this->render('index', ['ua' => $ua, 'user' => $this->user, 'ljsy' => $leijishouyi, 'dhsbj' => $dhsbj, 'zcze' => $zcze, 'data' => $data]);
    }

    /**
     * 输出个人交易明细记录
     * 输出信息均为成功记录
     */
    public function actionMingxi($page = 1, $size = 10)
    {
        $this->layout = '@app/modules/order/views/layouts/buy';
        $data = MoneyRecord::find()->where(['uid' => $this->user->id])
            ->select('created_at,type,in_money,out_money,balance')
            ->orderBy('id desc');
        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('mingxi', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    public function actionMyorder($type = null, $page = 1)
    {
        $this->layout = '@app/modules/user/views/layouts/myorder';
        $os = new OrderService();
        $list = $os->getUserOrderList($this->user->id, $type, $page);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $list;
        }

        return $this->render('order', ['list' => $list, 'type' => $type, 'profitFund' => $this->user->lendAccount->profit_balance]);
    }
}
