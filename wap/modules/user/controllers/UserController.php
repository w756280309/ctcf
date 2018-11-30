<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\UserCoupon;
use common\models\offline\OfflineOrder;
use common\models\offline\OfflineRepaymentPlan;
use common\models\offline\OfflineUser;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder as Ord;
use common\models\product\OnlineProduct as Loan;
use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\order\OrderManager;
use common\models\user\UserBank;
use common\models\user\UserBanks;
use common\service\BankService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use wap\modules\promotion\models\RankingPromo;
use Wcg\Xii\Risk\Model\Risk;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class UserController extends BaseController
{
    public function behaviors()
    {
        $except = [];
        $appVersionCode = $this->getAppVersion();

        if (!defined('IN_APP') || $appVersionCode >= 1.5) {
            $except = [
                'except' => [
                    'index',
                    'is-login',
                ],
            ];
        }

        return [
            'access' => array_merge([
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
            ], $except),
        ];
    }

    /**
     * 账户中心页
     */
    public function actionIndex()
    {
        $inApp = defined('IN_APP');
        $appVersionCode = $this->getAppVersion();
        $view = 'index';

        if (!$inApp || $appVersionCode >= 1.5) {
            $this->layout = '@app/views/layouts/fe';
            if (Yii::$app->user->isGuest){
                return $this->render($view);
            }
        } else {
            $view = 'index_yuan';
        }

        return $this->render($view, $this->index());
    }

    /**
     * 异步加载账户中心相关数据.
     */
    public function actionIsLogin()
    {
        $backArr = Yii::$app->user->isGuest ? [] : $this->index();
        $this->layout = false;

        $html = $this->render('_index', $backArr);
        return [
            'html' => $html,
            'sumLicai' => isset($backArr['sumLicai']) ? $backArr['sumLicai'] : '',
            'off_licai' => isset($backArr['off_licai'])? $backArr['off_licai'] : '',
            'eyeStatus' => isset($backArr['eyeStatus']) ? $backArr['eyeStatus'] : '',
        ];
    }

    private function index()
    {
        $user = $this->getAuthedUser();

        $ua = $user->lendAccount;
        $pointPromo = RankingPromo::findOne(['key' => 'loan_order_points']);

        $showPointsArea = false;
        try {
            if (!is_null($pointPromo)) {
                $showPointsArea = $pointPromo->isActive($user);
            }
        } catch (\Exception $e) {

        }

        $sumCoupon = 0;
        $sumLicai = 0;
        $inApp = defined('IN_APP');
        $appVersionCode = $this->getAppVersion();

        if (!$inApp || $appVersionCode >= 1.5) {
            //代金券总值
            $sumCoupon = UserCoupon::findCouponInUse($user->id, date('Y-m-d'))->sum('amount');
            $countCoupon = UserCoupon::findCouponInUse($user->id, date('Y-m-d'))->count();
            $sumLicai = null !== $ua ? bcadd($ua->freeze_balance, $ua->investment_balance, 2) : 0;
        }

        //账户中心页初始化页面时资产总额、累计收益、我的积分、可用余额、我的理财、门店理财的显示状态,第一次默认为显示状态
        $eyeStatus = 'open';
        $redis = Yii::$app->redis;
        if ($redis->hexists('userReferences', $user->id)) {
            $eyeStatus = $redis->hget('userReferences', $user->id);
        }

        $risk = Risk::find()
            ->where([
                'user_id' => $user->id,
                'isDel' => false,
            ])->one();
        if (null !== $risk) {
            $riskResult = Risk::riskResult();
            $riskContent = [
                'label' => $riskResult[$risk->grade]['conclusion'],
                'color' => '#8c8c8c',
            ];
        } else {
            $riskContent = [
                'label' => '未测试',
                'color' => '#ff0f20',
            ];
        }
        //判断用户是否绑卡成功
        $isBindCard = $user->qpay;

        return [
            'sumCoupon' => $sumCoupon,
            'countCoupon' => $countCoupon,
            'ua' => $ua,
            'user' => $user,
            'showPointsArea' => $showPointsArea,
            'sumLicai' => $sumLicai,
            'riskContent' => $riskContent,
            'off_licai' => (!is_null($user) && $user->offline) ? $user->offline->totalAssets : 0,
            'isBindCard' => $isBindCard,
            'eyeStatus' => $eyeStatus,
            'borrowerInfo' => $user->borrowerInfo,
            'borrowerAccount' => $user->borrowAccount,
        ];
    }

    /**
     * 获取开通快捷支付功能状态.
     */
    public function actionCheckKuaijie()
    {
        if (!Yii::$app->request->isAjax) {
            throw $this->ex404();
        }

        $user = $this->getAuthedUser();

        return BankService::checkKuaijie($user);
    }

    /**
     * 交替显示账户中心页资产金额显示状态
     * @param $status  open为显示，close为隐藏
     * @return array 当为close时，只返回状态，当为open时，返回状态及资产总额、累计收益、我的积分、可用余额、我的理财、门店理财的值
     */
    public function actionHiddenAmount($status)
    {
        $user = $this->getAuthedUser();
        $data = [];
        if ($user) {
            $ua = $user->lendAccount;
            $redis = Yii::$app->redis;
            if ($status === 'open') {
                $status = 'close';
                $redis->hset('userReferences', $user->id, $status);
            } elseif ($status === 'close') {
                $status = 'open';
                $redis->hset('userReferences', $user->id, $status);

                $data['totalAssets'] = StringUtils::amountFormat3($user->totalAssets); //资产总额
                $data['profit'] = StringUtils::amountFormat3($user->getProfit());  //累计收益
                $data['available_balance'] = StringUtils::amountFormat3($ua->available_balance); //可用余额
                $data['points'] = StringUtils::amountFormat2($user->points);   //我的积分
                $data['sumLicai'] = StringUtils::amountFormat2(bcadd($ua->freeze_balance, $ua->investment_balance, 2)); //我的理财
                $data['offLicai'] = StringUtils::amountFormat2(($user->offline) ? $user->offline->totalAssets : 0);  //门店理财

            }
            $data['eyeStatus'] = $status;

            return $data;
        }

        return $data;
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
    public function actionMyorder($type = 1, $page = 1, $back_url = null)
    {
        $type = intval($type);
        $pageSize = 5;

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        if ($back_url && !filter_var($back_url, FILTER_VALIDATE_URL)) {
            $backUrl = null;
        } else {
            $backUrl = Yii::$app->functions->dealurl($back_url);
        }

        $user = $this->getAuthedUser();
        $o = Ord::tableName();
        $l = Loan::tableName();

        if (2 === $type) {
            $query = Ord::find()
                ->innerJoinWith('loan')
                ->where(["$o.uid" => $user->id, "$o.status" => Ord::STATUS_SUCCESS])
                ->andWhere(["$l.status" => [Loan::STATUS_NOW, Loan::STATUS_FULL, Loan::STATUS_FOUND]])
                ->andWhere(["$l.is_jixi" => false])
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

        return $this->render('order', [
            'model' => $model,
            'type' => $type,
            'pages' => $pages,
            'backUrl' => $backUrl,
        ]);
    }
    /**
     * 线下理财
     *
     */
    public function actionMyofforder($type = 1, $page = 1, $backUrl = null)
    {
        if (!in_array($type, ['1', '3', '2'])) {
            throw $this->ex404();
        }
        $type = intval($type);
        $pageSize = 5;
        $user = $this->getAuthedUser();
        $sql = "select max(id) from offline_repayment_plan where uid = :uid GROUP BY loan_id";
        $plan = Yii::$app->db->createCommand($sql, ['uid' => $user->offlineUserId])->queryColumn();
        if ($user) {
            $query = OfflineOrder::find()
                ->select('offline_order.*')
                ->innerJoin('offline_loan', 'offline_loan.id = offline_order.loan_id')
                ->where([
                    'offline_order.user_id' => $user->offlineUserId,
                    'offline_order.isDeleted' => false,
                    ]);
            if ($type == 1) {   //收益中
                $query->innerJoin('offline_repayment_plan', 'offline_repayment_plan.loan_id = offline_loan.id')
                    ->andWhere([
                        'offline_loan.is_jixi' => true,
                        'offline_repayment_plan.id' => $plan,
                        'offline_repayment_plan.status' => 0,
                        ])
                    ->orderBy(['offline_loan.finish_date' => SORT_ASC]);
            } else if ($type == 3) {    //已还清
                $query->innerJoin('offline_repayment_plan', 'offline_repayment_plan.loan_id = offline_loan.id')
                    ->andWhere([
                        'offline_loan.is_jixi' => true,
                        'offline_repayment_plan.id' => $plan,
                        'offline_repayment_plan.status' => [1, 2],
                        ])
                    ->orderBy(['offline_order.orderDate' => SORT_ASC]);
            } else if ($type == 2) {    //待成立
                $query->andWhere(['is_jixi' => false])
                    ->orderBy(['offline_order.orderDate' => SORT_ASC]);
            }
            $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
            $model = $query->offset($pages->offset)->limit($pages->limit)->all();

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
                $html = $this->renderFile('@wap/modules/user/views/user/_offline_order_list.php', ['model' => $model, 'type' => $type]);
                return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
            }
            return $this->render('off-order', [
                'model' => $model,
                'pages' => $pages,
                'backUrl' => $backUrl,
                'type' => $type,
            ]);
        }
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
            throw $this->ex404();
        }

        //获得加息券收益
        $bonusProfit = $deal->getBonusProfit();

        $product = Loan::findOne($deal->online_pid);
        if (null === $product) {
            throw $this->ex404();
        }

        $totalFund = OrderManager::getTotalInvestment($product, $this->getAuthedUser());

        $profit = null;
        if (!in_array($product->status, [2, 3, 7])) {
            $profit = $deal->getProceeds();
        }

        $plan = null;
        $hkDate = null;
        if ($product->jixi_time) {
            $cond = ['online_pid' => $asset['loan_id'], 'uid' => $this->getAuthedUser()->id];
            if (!empty($asset['note_id'])) {
                $cond['asset_id'] = $asset['id'];
            } else {
                $cond['order_id'] = $deal->id;
                $cond['asset_id'] = null;
            }

            $plan = Plan::find()
                ->where($cond)
                ->orderBy(['qishu' => SORT_ASC])
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
            'bonusProfit' => $bonusProfit,
            'product' => $product,
            'plan' => $plan,
            'profit' => $profit,
            'hkDate' => $hkDate,
            'totalFund' => $totalFund,
            'asset' => $asset,
            'fromTransfer' => $fromTransfer,
        ]);
    }
    //线下订单详情
    public function actionOfflineOrderdetail($id)
    {
        $model = OfflineOrder::findOne($id);
        //还款计划
        $plans = OfflineRepaymentPlan::find()->where([
            'order_id' => $id,
            'loan_id' => $model->loan->id,
            'uid' => $model->user_id,
        ])->all();
        return $this->render('offline_order_detail', [
            'model' => $model,
            'plans' => $plans,
            ]);
    }

    /**
     * 开通免密支付功能.
     */
    public function actionMianmi()
    {
        $this->layout = '@app/views/layouts/fe';

        $data = BankService::check($this->getAuthedUser(), BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE_Y);

        return $this->render('mianmi', $data);
    }

    /**
     * 资产总览页面.
     */
    public function actionAssets()
    {
        $this->layout = '@app/views/layouts/fe';

        return $this->render('assets', [
            'user' => $this->getAuthedUser(),
        ]);
    }

    /**
     * 我的收益页面.
     *
     * 1. 每次输出最近的10条回款记录;
     */
    public function actionProfit($page = 1, $size = 10)
    {
        $this->layout = '@app/views/layouts/fe';
        $p = Plan::tableName();

        $query = Plan::find()
            ->innerJoinWith('loan')
            ->where(['uid' => $this->getAuthedUser()->id])
            ->andWhere(['in', "$p.status", [Plan::STATUS_YIHUAN, Plan::STATUS_TIQIAM]]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $size]);
        $profits = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'actualRefundTime' => SORT_DESC,
                'refund_time' => SORT_DESC,
            ])
            ->all();

        $tp = $pages->pageCount;
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        $header = [
            'count' => $pages->totalCount,
            'size' => $pages->pageSize,
            'tp' => $tp,
            'cp' => $pages->page + 1,
        ];

        $backArr = [
            'profits' => $profits,
        ];

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_profit', $backArr);

            return [
                'header' => $header,
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        return $this->render('profit', array_merge($backArr, [
            'header' => $header,
            'user' => $this->getAuthedUser(),
        ]));
    }
}
