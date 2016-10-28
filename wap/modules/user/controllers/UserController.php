<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder as Ord;
use common\models\product\OnlineProduct as Loan;
use common\models\order\OnlineRepaymentPlan as Plan;
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
            ->orderBy('id desc');
        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/user/views/user/_mingxi_list.php', ['model' => $model]);

            return ['header' => $pg->jsonSerialize(), 'html' => $html, 'code' => $code, 'message' => $message];
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
        $o = Ord::tableName();
        $l = Loan::tableName();

        if (2 === $type) {
            $query = Ord::find()
                ->innerJoinWith('loan')
                ->where(["$o.uid" => $user->id, "$o.status" => Ord::STATUS_SUCCESS])
                ->andWhere(["$l.status" => [Loan::STATUS_NOW, Loan::STATUS_FULL, Loan::STATUS_FOUND]])
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
                $cond = ['online_pid' => $val['loan_id'], 'uid' => $user->id];
                if (!empty($val['note_id'])) {
                    $cond['asset_id'] = $val['id'];
                } else {
                    $cond['order_id'] = $val['order_id'];
                    $cond['asset_id'] = null;
                }

                $data = Plan::find()
                    ->where($cond)
                    ->asArray()
                    ->all();

                $model[$key]['shouyi'] = array_sum(array_column($data, 'lixi'));
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
     *
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOrderdetail($id = null, $asset_id = null, $from_transfer = false)
    {
        if (empty($id) && empty($asset_id)) {
            throw $this->ex404();
        }

        $fromTransfer = boolval($from_transfer);
        $asset = null;

        if (!empty($asset_id)) {
            $asset = Yii::$container->get('txClient')->get('assets/detail', [
                'id' => $asset_id,
            ]);

            if (null === $asset) {
                throw $this->ex404();
            }

            $id = $asset['order_id'];
        }

        $deal = Ord::findOne($id);
        if (null === $deal) {
            throw $this->ex404();    //当对象为空时,抛出404异常
        }

        $product = Loan::findOne($deal->online_pid);
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
        if (in_array($product->status, [Loan::STATUS_HUAN, Loan::STATUS_OVER])) {
            $cond = ['online_pid' => $asset['loan_id'], 'uid' => $this->getAuthedUser()->id];
            if (!empty($asset['note_id'])) {
                $cond['asset_id'] = $asset['id'];
            } else {
                $cond['order_id'] = $deal->id;
                $cond['asset_id'] = null;
            }

            $plan = Plan::find()
                ->where($cond)
                ->asArray()
                ->all();

            if (!empty($plan)) {
                $hkDate = end($plan)['refund_time'];
                $flag = 0;
                foreach($plan as $val) {
                    if (0 === $flag && Plan::STATUS_WEIHUAN === $val['status']) {
                        $hkDate = $val['refund_time'];
                        $flag = 1;
                    }
                }
            }

            $profit = array_sum(array_column($plan, 'lixi'));
        }

        return $this->render('order_detail', [
            'deal' => $deal,
            'product' => $product,
            'plan' => $plan,
            'profit' => $profit,
            'hkDate' => $hkDate,
            'totalFund' => $totalFund,
            'asset' => $asset,
            'fromTransfer' => $fromTransfer,
        ]);
    }
}