<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OrderManager;
use common\service\BankService;
use common\lib\bchelp\BcRound;
use Yii;
use yii\data\Pagination;

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
        $ua = $this->getAuthedUser()->lendAccount;
        $leijishouyi = $ua->getTotalProfit();//累计收益
        $dhsbj = $bc->bcround(bcadd($ua->investment_balance, $ua->freeze_balance), 2);//在wap端理财资产等于实际理财资产+用户投资冻结金额freeze_balance
        $zcze = $ua->getTotalFund();//账户余额+理财资产

        $data = BankService::checkKuaijie($user);

        return $this->render('index', ['ua' => $ua, 'user' => $user, 'ljsy' => $leijishouyi, 'dhsbj' => $dhsbj, 'zcze' => $zcze, 'data' => $data]);
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
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('mingxi', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    /**
     * 我的理财页面.
     *
     * 1.一页显示5条记录;
     */
    public function actionMyorder($type = 1, $page = 1)
    {
        $type = intval($type);
        $pageSize = 5;

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        $user = $this->getAuthedUser();
        $o = OnlineOrder::tableName();
        $l = OnlineProduct::tableName();

        if (2 === $type) {
            $query = OnlineOrder::find()
                ->innerJoinWith('loan')
                ->where(["$o.uid" => $user->id, "$o.status" => OnlineOrder::STATUS_SUCCESS])
                ->andWhere(["$l.status" => [OnlineProduct::STATUS_NOW, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND]])
                ->orderBy("$o.id desc");

            $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
            $model = $query->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        } else {
            $assets = Yii::$container->get('txClient')->get('assets/list', [
                'user_id' => $user->id,
                'type' => $type,
                'page' => $page,
                'page_size' => $pageSize,
            ]);

            $pages = new Pagination(['totalCount' => $assets['totalCount'], 'pageSize' => $pageSize]);
            $model = $assets['data'];

            foreach ($model as $key => $val) {
                $model[$key]['order'] = OnlineOrder::findOne($val['order_id']);
            }
        }

        $tp = $pages->pageCount;
        $header = [
            'count' => $pages->totalCount,
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/user/views/user/_order_list.php', ['model' => $model, 'type' => $type]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('order', ['model' => $model, 'type' => $type, 'pages' => $pages]);
    }

    /**
     * 投资详情页
     * @param type $id
     * @return type
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOrderdetail($id, $asset_id = null)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $deal = OnlineOrder::findOne($id);
        if (null === $deal) {
            throw $this->ex404();    //当对象为空时,抛出404异常
        }

        $product = OnlineProduct::findOne($deal->online_pid);
        if (null === $product) {
            throw $this->ex404();    //当对象为空时,抛出404异常
        }

        $totalFund = OrderManager::getTotalInvestment($product, $this->getAuthedUser());

        $profit = null;
        if (!in_array($product->status, [2, 3, 7])) {
            $profit = $deal->getProceeds();
        }

        $plan = null;
        $hkDate = null;
        if (in_array($product->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
            $plan = OnlineRepaymentPlan::findAll(['online_pid' => $deal->online_pid, 'uid' => $this->getAuthedUser()->id, 'order_id' => $deal->id]);

            if (!empty($plan)) {
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

        return $this->render('order_detail', [
            'deal' => $deal,
            'product' => $product,
            'plan' => $plan,
            'profit' => $profit,
            'hkDate' => $hkDate,
            'totalFund' => $totalFund,
            'assetId' => $asset_id,
        ]);
    }
}