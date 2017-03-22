<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\UserCoupon;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder as Ord;
use common\models\product\OnlineProduct as Loan;
use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\order\OrderManager;
use common\service\BankService;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UserController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
                'except' => [
                    'index',
                ],
            ],
        ];
    }

    /**
     * 账户中心页
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/fe';
        if (Yii::$app->user->isGuest){
            return $this->render('index');
        }
        $user = $this->getAuthedUser();
        $ua = $user->lendAccount;
        $pointPromo = RankingPromo::findOne(['key' => 'loan_order_points']);
        //代金券总值
        $sumCoupon = UserCoupon::findCouponInUse($user,date('Y-m-d'))->sum('amount');

        try {
            $showPointsArea = $pointPromo->isActive($user);
        } catch (\Exception $e) {
            $showPointsArea = false;
        }
        return $this->render('index', [
            'sumCoupon' => $sumCoupon,
            'ua' => $ua,
            'user' => $user,
            'showPointsArea' => $showPointsArea,
        ]);
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
            ->andWhere(["$p.status" => Plan::STATUS_YIHUAN]);

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