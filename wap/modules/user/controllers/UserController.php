<?php

namespace app\modules\user\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\core\UserAccountCore;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OrderManager;
use common\service\BankService;
use common\lib\bchelp\BcRound;

class UserController extends BaseController
{
    /**
     * 账户中心页
     */
    public function actionIndex()
    {
        $bc = new BcRound();
        bcscale(14);
        $user = $this->getAuthedUser();
        $uacore = new UserAccountCore();
        $ua = $this->getAuthedUser()->lendAccount;
        $leijishouyi = $uacore->getTotalProfit($user->id);//累计收益
        $dhsbj = $bc->bcround(bcadd($ua->investment_balance, $ua->freeze_balance), 2);//在wap端理财资产等于实际理财资产+用户投资冻结金额freeze_balance
        $zcze = $uacore->getTotalFund($user->id);//账户余额+理财资产

        $data = BankService::checkKuaijie($user);

        return $this->render('index', ['ua' => $ua, 'user' => $this->getAuthedUser(), 'ljsy' => $leijishouyi, 'dhsbj' => $dhsbj, 'zcze' => $zcze, 'data' => $data]);
    }

    /**
     * 输出个人交易明细记录
     * 输出信息均为成功记录
     */
    public function actionMingxi($page = 1, $size = 10)
    {
        $data = MoneyRecord::find()->where(['uid' => $this->getAuthedUser()->id])
            ->andWhere(['type' => MoneyRecord::getLenderMrType()])
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

    /**
     * 我的理财页面
     * @param type $type
     * @param type $page
     * @return type
     */
    public function actionMyorder($type = null, $page = 1)
    {
        $os = new OrderService();
        $list = $os->getUserOrderList($this->getAuthedUser()->id, $type, $page);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $list;
        }

        return $this->render('order', ['list' => $list, 'type' => $type, 'profitFund' => $this->getAuthedUser()->lendAccount->profit_balance]);
    }

    /**
     * 投资详情页
     * @param type $id
     * @return type
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOrderdetail($id)
    {
        if (empty($id)) {
            throw new \yii\web\NotFoundHttpException('The argument is not existed.');
        }

        $deal = OnlineOrder::findOne($id);
        $product = OnlineProduct::findOne($deal->online_pid);

        $profit = null;
        if (!in_array($product->status, [2, 3, 7])) {
            $profit = OnlineRepaymentPlan::getTotalLixi($product, $deal);
        }

        $plan = null;
        $hkDate = null;
        if (in_array($product->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
            $plan = OnlineRepaymentPlan::findAll(['online_pid' => $deal->online_pid, 'uid' => $this->getAuthedUser()->id, 'order_id' => $deal->id]);

            if ($plan) {
                $hkDate = end($plan)['refund_time'];
                $flag = 0;
                foreach($plan as $val) {
                    if (0 === $flag && OnlineRepaymentPlan::STATUS_WEIHUAN === $val['status']) {
                        $hkDate = $val['refund_time'];
                        $flag = 1;
                    }
                }
            }
        }

        return $this->render('order_detail', ['deal' => $deal, 'product' => $product, 'plan' => $plan, 'profit' => $profit, 'hkDate' => $hkDate]);
    }
}
