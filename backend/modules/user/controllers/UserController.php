<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use backend\modules\user\core\v1_0\UserAccountBackendCore;
use common\lib\err\Err;
use common\lib\user\UserStats;
use common\models\adminuser\AdminAuth;
use common\models\adminuser\AdminLog;
use common\models\adminuser\Auth;
use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\bank\Bank;
use common\models\epay\EpayUser;
use common\models\log\LoginLog;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\promo\InviteRecord;
use common\models\user\Borrower;
use common\models\user\CoinsRecord;
use common\models\user\MoneyRecord;
use common\models\user\OriginalBorrower;
use common\models\user\QpayBinding;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\UserInfo;
use common\models\user\UserSearch;
use common\models\user\DrawRecord;
use common\models\user\UserFreepwdRecord;
use common\models\tx\UserAsset;
use common\models\tx\CreditNote;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UserController extends BaseController
{
    public function behaviors()
    {
        $params = array_merge(
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ], parent::behaviors()
        );

        return $params;
    }

    /**
     * 积分明细.
     */
    public function actionPointList($userId, $tabClass = null)
    {
        $user = $this->findOr404(User::class, $userId);
        $query = PointRecord::find()
            ->where([
                'user_id' => $userId,
                'isOffline' => false,
            ])
            ->orderBy(['id' => SORT_DESC]);
        $ref_type = Yii::$app->request->get('ref_type');
        if ($ref_type != null) {
            $query->andWhere(['ref_type' => $ref_type]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $orderIds = [];
        $orders = [];
        foreach ($dataProvider->models as $model) {
            if (in_array($model->ref_type, [PointRecord::TYPE_LOAN_ORDER, PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1])) {
                $orderIds[] = $model->ref_id;
            }
        }
        if (!empty($orderIds)) {
            $o = OnlineOrder::tableName();

            $orders = OnlineOrder::find()
                ->joinWith('loan')
                ->where(["$o.id" => $orderIds])
                ->indexBy('id')
                ->all();
        }
        $this->layout = false;

        return $this->render('_point_list', [
            'dataProvider' => $dataProvider,
            'orders' => $orders,
            'user' => $user,
            'ref_type' => $ref_type,
            'tabClass' => $tabClass
        ]);
    }

    /**
     * 财富值明细.
     */
    public function actionCoinList($userId, $isOffline = 0)
    {
        if (!in_array($isOffline, [0, 1])) {
            throw $this->ex404();
        }

        $query = CoinsRecord::find()
            ->where([
                'user_id' => $userId,
                'isOffline' => $isOffline,
            ])
            ->orderBy(['createTime' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->layout = false;

        return $this->render('_coin_list', [
            'dataProvider' => $dataProvider,
            'isOffline' => $isOffline,
        ]);
    }

    /**
     * 投资人列表
     */
    public function actionListt()
    {
        $request = $this->validateRequest(Yii::$app->request->get());
        $userSearch = new UserSearch();
        $query = $userSearch->search($request);

        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 15,
        ]);

        $user = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['user.created_at' => SORT_DESC])->all();

        $affiliators = UserAffiliation::find()
            ->innerJoinWith('affiliator')
            ->where(['user_id' => ArrayHelper::getColumn($user, 'id')])
            ->indexBy('user_id')
            ->all();

        $affList = Affiliator::find()->all();

        return $this->render('list', [
            'model' => $user,
            'pages' => $pages,
            'category' => User::USER_TYPE_PERSONAL,
            'request' => $request,
            'affiliators' => $affiliators,
            'affList' => $affList,
        ]);
    }

    //投资用户信息导出
    public function actionExport()
    {
        $path  = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $fileName = 'all_investor_user.xlsx';
        if (file_exists($path . '/' . $fileName)) {
            return Yii::$app->response->xSendFile('/downloads/' . $fileName, $fileName, [
                'xHeader' => 'X-Accel-Redirect',
            ]);
        }
        echo '等待定时任务导出数据';
        exit;
    }

    /**
     * 融资人列表
     */
    public function actionListr()
    {
        $request = $this->validateRequest(Yii::$app->request->get());
        $admin = Yii::$app->user->getIdentity();
        $showOrgList = $admin->hasAuth('user/user/show-org-list');
        $hideOrgList = false;

        if ((!isset($request['search']) || empty($request['search'])) && !$showOrgList) {
            $pages = new Pagination(['totalCount' => 0, 'pageSize' => '15']);
            $model = [];
            $hideOrgList = true;
        } else {
            $query = User::find()
                ->innerJoinWith('borrowAccount')
                ->innerJoinWith('borrowerInfo')
                ->where([
                    'user.type' => User::USER_TYPE_ORG,
                    'is_soft_deleted' => 0,
                ]);

            if (isset($request['name']) && !empty($request['name'])) {
                $query->andFilterWhere(['like', 'org_name', trim($request['name'])]);
            }
            if (!empty($request['accountType'])) {
                $query->andFilterWhere(['borrower.type' => (int) $request['accountType']]);
            }

            $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
            $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('created_at desc')->all();
        }

        return $this->render('list', [
            'model' => $model,
            'category' => User::USER_TYPE_ORG,
            'pages' => $pages,
            'request' => $request,
            'hideOrgList' => $hideOrgList,
        ]);
    }

    /**
     * 融资会员软删除，即将user表type=2(融资会员)的is_soft_delete设置为1,并且融资会员账户余额等于0时，将其软删除
     */
    public function actionSoftDeleteOrgUser($id)
    {
        $model = $this->findOr404(User::class, $id);
        if ($model->type === User::USER_TYPE_ORG && $model->is_soft_deleted === 0) {
            $ua = $model->borrowAccount;
            if ($ua['available_balance'] > 0) {
                $data = [
                    'status' => 'fail',
                    'msg' => '融资会员账户余额大于0,不可删除！',
                ];
                return $data;
            }
            $model->is_soft_deleted = 1;
            $status = $model->save(false);
            if ($status) {
                $data = [
                    'status' => 'success',
                    'msg' => '删除成功',
                ];
            } else {
                $data = [
                    'status' => 'fail',
                    'msg' => '删除失败',
                ];
            }
            return $data;
        } else {
            $data = [
                'status' => 'fail',
                'msg' => '非融资会员或已删除'
            ];
            return $data;
        }
    }

    private function validateRequest($request)
    {
        $res = [];

        foreach ($request as $key => $val) {
            $res[$key] = strip_tags($val);
        }

        return $res;
    }

    /**
     * 查看用户详情.
     */
    public function actionDetail($id, $tabClass = null)
    {
        $user = User::findOne($id);
        $status = Yii::$app->request->get('status');
        if (empty($user)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }
        if ($user->isOrgUser()) {
            return $this->orgUserDetail($user);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->dealAjax($user);
            }
            return $this->normalUserDetail($user, $tabClass);
        }
    }

    /**
     * 给邀请人补充邀请关系.
     */
    public function actionAddInvite($userId, $mobile = null)
    {
        $user = $this->findOr404(User::class, $userId);
        $invitee = null;

        if (!empty($mobile)) {
            $this->time = 2;
            $this->alert = 2;
            $this->msg = '';

            if ($mobile === SecurityUtils::decrypt($user->safeMobile)) {
                $this->msg = '【'.Err::code('000008').'】被邀请人不能是本人';
            } else {
                $invitee = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile), 'type' => User::USER_TYPE_PERSONAL]);

                if (is_null($invitee)) {
                    $this->msg = '【'.Err::code('000002').'】被邀请人还没有注册';
                }
            }

            if (Yii::$app->request->isAjax) {
                if ($this->msg) {
                    $back = ['code' => 1, 'message' => $this->msg];
                } else {
                    $back = ['code' => 0, 'mobile' => $invitee->mobile, 'realName' => $invitee->real_name];
                }

                return $back;
            }

            if ('' === $this->msg) {
                try {
                    $this->addInvite($user, $invitee);
                    $this->alert = 1;
                    $this->msg = '操作成功';
                    $this->toUrl = '/user/user/detail?id='.$user->id.'&type=1';
                } catch (\Exception $e) {
                    $this->msg = $e->getMessage();
                }
            }
        }

        return $this->render('add_invite', [
            'user' => $user,
            'invitee' => $invitee,
        ]);
    }

    /**
     * 补邀请关系.
     *
     * 注意：
     * 1. 只有被邀请者没有被其他人邀请过且被邀请者没有邀请过邀请人的时候才能补邀请关系;
     * 2. 取被邀请者前三次订单补发奖励（无法判断奖励是否已发放，只能依赖于“没有邀请关系的用户没有发邀请奖励”来判断）;
     */
    private function addInvite($user, $invitee)
    {
        $record = InviteRecord::findOne([
            'user_id' => $invitee->id,
            'invitee_id' => $user->id,
        ]);

        if (!is_null($record)) {
            throw new \Exception('【'.Err::code('000007').'】邀请人与被邀请人已存在邀请关系');
        }

        if (InviteRecord::inviteeCount($invitee->id) > 0) {
            throw new \Exception('【'.Err::code('000007').'】被邀请人已经有人邀请了');
        }

        $promo = RankingPromo::findOne(['key' => 'promo_invite_12']);
        if (!is_null($promo) && !empty($promo->promoClass)) {
            $model = new $promo->promoClass($promo);
            if (!empty($model)) {
                $inviteRecord = new InviteRecord([
                    'user_id' => $user->id,
                    'invitee_id' => $invitee->id,
                ]);
                if (!$inviteRecord->save()) {
                    throw new \Exception('【'.Err::code('000003').'】邀请记录添加失败');
                }
                $userInfo = UserInfo::find()
                    ->where(['user_id' => $invitee->id])
                    ->one();
                if (null !== $userInfo) {
                    $userInfo->isAffiliator = true;
                    $userInfo->save(false);
                }

                $model->addInviteeCoupon($invitee);  //给被邀请人发放代金券奖励

                //被邀请者前三次正式标投资记录
                $orders = OnlineOrder::find()
                    ->leftJoin('online_product', 'online_order.online_pid = online_product.id')
                    ->where(['online_order.status' => OnlineOrder::STATUS_SUCCESS, 'online_order.uid' => $invitee->id])
                    ->andWhere(['online_product.is_xs' => 0])
                    ->orderBy(['online_order.id' => SORT_ASC])
                    ->limit(3)
                    ->all();
                foreach ($orders as $order) {
                    //发奖励
                    $model->doAfterSuccessLoanOrder($order);
                }
            }
        }
    }

    private function dealAjax(User $user)
    {
        $key = Yii::$app->request->get('key');
        switch ($key) {
            case 'money_record' :
                return $this->getMoneyRecord($user);
                break;
            case 'invite_record':
                return $this->getInviteRecord($user);
                break;
            case 'offline_user' :
                return $this->getOfflineUser($user);
            case 'recharge_record':
                return $this->getRechargeRecord($user);
                break;
            case 'draw_record':
                return $this->getDrawRecord($user);
                break;
            case 'credit_note':
                return $this->getCreditNote($user);
                break;
            case 'commercial_record':
                return $this->getCommercialNote($user);
                break;
            default :
                break;
        }
        return [];
    }

    /**
     * 提现记录
     */
    private function getDrawRecord(User $user)
    {
        $b = Bank::tableName();
        $d = DrawRecord::tableName();

        $query = (new \yii\db\Query)
            ->select("$d.*, $b.bankName")
            ->from($d)
            ->leftJoin($b, "$d.bank_id = $b.id")
            ->where(['uid' => $user->id])
            ->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->renderFile('@backend/modules/user/views/user/_draw_record.php', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 获取充值记录
     */
    private function getRechargeRecord(User $user)
    {
        $r = RechargeRecord::tableName();
        $u = UserBanks::tableName();
        $query = (new \yii\db\Query)
            ->select(["$r.*", "$u.bank_name"])
            ->from($r)
            ->leftJoin($u, "$r.uid = $u.uid")
            ->where(["$r.uid" => $user->id])
            ->orderBy(["$r.created_at" => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $records = $dataProvider->getModels();
        if ($records > 0) {
            $banks = Bank::find()->where(['in', 'id', ArrayHelper::getColumn($records, 'bank_id')])->asArray()->all();
            $banks = ArrayHelper::index($banks, 'id');
        } else {
            $banks = [];
        }

        return $this->renderFile('@backend/modules/user/views/user/_recharge_record.php', [
            'dataProvider' => $dataProvider,
            'banks' => $banks,
        ]);
    }

    /**
     * 获取用户邀请好友记录
     */
    private function getInviteRecord(User $user)
    {
        $query = InviteRecord::find()->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $records = $dataProvider->getModels();
        $rechargeData = [];
        $loanData = [];
        if (count($records) > 0) {
            $ids = ArrayHelper::getColumn($records, 'invitee_id');
            $rechargeData = RechargeRecord::find()->select(['uid', 'sum(fund) as recharge_sum'])->where(['in', 'uid', $ids])->andWhere(['status' => RechargeRecord::STATUS_YES])->groupBy('uid')->asArray()->all();
            $rechargeData = ArrayHelper::index($rechargeData, 'uid');
            $loanData = OnlineOrder::find()->select(['uid', 'sum(order_money) as loan_sum'])->where(['in', 'uid', $ids])->andWhere(['status' => OnlineOrder::STATUS_SUCCESS])->groupBy('uid')->asArray()->all();
            $loanData = ArrayHelper::index($loanData, 'uid');
        }

        return $this->renderFile('@backend/modules/user/views/user/_invite_record.php', [
            'dataProvider' => $dataProvider,
            'rechargeData' => $rechargeData,
            'loanData' => $loanData,
            'user' => $user,
        ]);
    }
    /**
     * 获取会员详情页-用户线下会员列表
     */
    private function getOfflineUser(User $user)
    {
        $query = OfflineUser::find()->where(['idCard' => SecurityUtils::decrypt($user->safeIdCard)])->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->renderFile('@backend/modules/user/views/user/_offline_user.php', [
            'dataProvider' => $dataProvider,
            'user' => $user,
        ]);
    }

    /**
     * 获取商业委托免密开通状态
     */
    private function getCommercialNote(User $user)
    {
        $r = UserFreepwdRecord::tableName();
        $u = QpayBinding::tableName();
        $query = (new \yii\db\Query)
            ->select(["$r.*", "$u.card_number"])
            ->from($r)
            ->leftJoin($u, "$r.uid = $u.uid")
            ->where(["$r.uid" => $user->id])
            ->orderBy(["$r.created_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $records = $dataProvider->getModels();
        return $this->renderFile('@backend/modules/user/views/user/_commercial_record.php', [
            'dataProvider' => $dataProvider,
            'user' => $records,
        ]);
    }

    /**
     * 获取用户资金流水记录
     */
    private function getMoneyRecord(User $user)
    {
        $status = Yii::$app->request->get('status');
        $query = MoneyRecord::find()->where(['uid' => $user->id]);
        if ($status != null) {
            $query->andWhere(['type' => $status]);
        }
        $query->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $records = $dataProvider->getModels();
        $recordTypes = Yii::$app->params['mingxi'];
        if (count($records) > 0) {
            $recordIds = ArrayHelper::getColumn($records, 'id');
            //标的订单
            $loanOrderData = Yii::$app->db->createCommand("SELECT p.title, p.id, r.osn, o.investFrom
FROM money_record AS r
INNER JOIN online_order AS o ON o.sn = r.osn
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE r.type =2
AND r.id
IN (" . implode(',', $recordIds) . ")")->queryAll();
            $data = ArrayHelper::index($loanOrderData, 'osn');
            //标的回款
            $repaymentData = Yii::$app->db->createCommand("SELECT r.osn, p.title, p.id
FROM  `money_record` AS r
INNER JOIN online_repayment_plan AS rp ON r.osn = rp.sn
INNER JOIN online_product AS p ON p.id = rp.online_pid
WHERE r.type =4 AND r.id
IN (" . implode(',', $recordIds) . ")")->queryAll();
            $data = array_merge($data, ArrayHelper::index($repaymentData, 'osn'));

        } else {
            $data = [];
        }

        return $this->renderFile('@backend/modules/user/views/user/_money_record.php', [
            'recordTypes' => $recordTypes,
            'data' => $data,
            'dataProvider' => $dataProvider,
            'normalUser' => $user,
            'status' => $status,
        ]);
    }

    /**
     * 获取用户发起债券记录
     */
    private function getCreditNote(User $user)
    {
        $page = Yii::$app->request->get('page');
        //获取用户转让统计
        $txClient = Yii::$container->get('txClient');
        $noteData = $txClient->get('credit-note/user', [
            'user_id' => $user->id,
            'with' => 'list',
            'page' => intval($page) ? intval($page) : 1,
            'page_size' => 10,
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $noteData['noteList'],
        ]);
        $pages = new Pagination([
            'totalCount' => $noteData['totalNoteCount'],
            'pageSize' => 10,
        ]);

        if (count($noteData['noteList']) > 0) {
            $loanIds = ArrayHelper::getColumn($noteData['noteList'], 'loan_id');
            $loans = OnlineProduct::find()->where(['in', 'id', $loanIds])->all();
            $loans = ArrayHelper::index($loans, 'id');
        } else {
            $loans = [];
        }
        return $this->renderFile('@backend/modules/user/views/user/_credit_note.php', [
            'dataProvider' => $dataProvider,
            'loans' => $loans,
            'pages' => $pages,
        ]);
    }

    /**
     * 融资会员详情
     * @param User $user
     */
    private function orgUserDetail(User $user)
    {
        $id = $user->id;
        $uabc = new UserAccountBackendCore();
        $recharge = $uabc->getRechargeSuccess($id);
        $draw = $uabc->getDrawSuccess($id);

        $rcMax = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id])->min('start_date');
        $ret = $uabc->getReturnInfo($id);
        $product = $uabc->getProduct($id);
        $ua = $user->borrowAccount;  //获取融资用户账户信息
        $epayUserID = $user->epayUser->epayUserId;
        $userAff = null;
        $userYuE = $ua['available_balance'];

        return $this->render('org_user_detail', [
            'czTime' => $rcMax,
            'czNum' => $recharge['count'],
            'czMoneyTotal' => $recharge['sum'],
            'txNum' => $draw['count'],
            'txMoneyTotal' => $draw['sum'],
            'userYuE' => $userYuE,
            'rzNum' => $product['count'],
            'rzMoneyTotal' => $product['sum'],
            'ret' => $ret,
            'orgUser' => $user,
            'epayUserID' => $epayUserID,
        ]);
    }

    /**
     * 普通会员详情
     * @param User $user
     */
    private function normalUserDetail(User $user, $tabClass = null)
    {
        $id = $user->id;
        $borrowerInfo = $user->borrowerInfo;
        //查询是否绑定微信息
        if ((new \yii\db\Query())
            ->from('social_connect')
            ->where(['user_id' => $id, 'provider_type' => 'wechat'])
            ->one()) {
            $is_wechat = 1;
        }else{
            $is_wechat = 0;
        }
        $uabc = new UserAccountBackendCore();
        $recharge = $uabc->getRechargeSuccess($id);
        $draw = $uabc->getDrawSuccess($id);
        $rcMax = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES, 'uid' => $id])->max('updated_at');
        $order = $uabc->getOrderSuccess($id);
        $ua = $user->lendAccount;    //获取投资用户账户信息
        $userAff = UserAffiliation::findOne(['user_id' => $user->id]);
        $txClient = Yii::$container->get('txClient');
        $txRes = $txClient->get('credit-order/records', [
            'user_id' => $id,
            'require_list' => false,
        ]);
        $order['creditSuccessCount'] = $txRes['successCount'];
        $order['creditTotalAmount'] = bcdiv($txRes['totalInvestAmount'], 100, 2);
        $order['latestCreditOrderTime'] = $txRes['latestOrderTime'];

        $o = OnlineOrder::tableName();
        $p = OnlineProduct::tableName();
        $leiji = OnlineOrder::find()
            ->innerJoinWith('loan')
            ->where(["$p.del_status" => 0, "$p.isTest" => false, "$o.uid" => $id, "$o.status" => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(["(case when $p.refund_method = 1 then if($p.expires >= 160, 1, 0) when $p.refund_method > 1 then if($p.expires >= 6, 1, 0) end)" => 1])
            ->andWhere(['>=', "$o.created_at", strtotime(date('Y') . '0101')])
            ->andWhere(['<=', "$o.created_at", mktime(23, 59, 59, 12, 31, date('Y'))])
            ->sum('order_money');
        $tztimeMax = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS, 'uid' => $id])->max('updated_at');

        //获取用户转让统计
        $noteData = $txClient->get('credit-note/user', [
            'user_id' => $user->id,
            'with' => 'transfer_count,transfer_sum',
        ]);

        return $this->render('detail', [
            'borrowerInfo' => $borrowerInfo,
            'czTime' => $rcMax,
            'czNum' => $recharge['count'],
            'czMoneyTotal' => $recharge['sum'],
            'txNum' => $draw['count'],
            'txMoneyTotal' => $draw['sum'],
            'tzTime' => $tztimeMax,
            'tzNum' => $order['count'],
            'tzMoneyTotal' => $order['sum'],
            'normalUser' => $user,
            'userAff' => $userAff,
            'creditSuccessCount' => $order['creditSuccessCount'],
            'creditTotalAmount' => $order['creditTotalAmount'],
            'latestCreditOrderTime' => $order['latestCreditOrderTime'],
            'leiji' => $leiji > 0 ? $leiji : '0.00',
            'transferCount' => $noteData['transferCount'],
            'transferSum' => bcdiv($noteData['transferSum'], 100, 2),
            'userAccount' => $ua,
            'is_wechat' => $is_wechat,
            'tabClass' => $tabClass
        ]);
    }


    /**
     * 查看指定用户的债权投资明细
     * @param $id
     * @return Response
     */
    public function actionCreditRecords($id)
    {
        $user = User::findOne($id);
        $txRes = Yii::$container->get('txClient')->get('credit-order/records', [
            'user_id' => $id,
            'page' => Yii::$app->request->get('page'),
            'page_size' => Yii::$app->request->get('page_size'),
        ]);
        if (count($txRes['data']) > 0) {
            $loan = OnlineProduct::find()->where(['in', 'id', ArrayHelper::getColumn($txRes['data'], 'loan_id')])->all();
            $loan = ArrayHelper::index($loan, 'id');
            $userAsset = UserAsset::find()->where(['in', 'credit_order_id', ArrayHelper::getColumn($txRes['data'], 'id')])->all();
            $userAsset = ArrayHelper::index($userAsset, 'credit_order_id');
        } else {
            $loan = [];
            $userAsset = [];
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $txRes['data'],
        ]);
        $pages = new Pagination([
            'totalCount' => $txRes['totalCount'],
            'pageSize' => 10,
        ]);
        return $this->renderFile('@backend/modules/user/views/user/credit_records.php', [
            'user' => $user,
            'loan' => $loan,
            'userAsset' => $userAsset,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * 编辑融资用户.
     */
    public function actionEdit($id = null, $type = 2)
    {
        $model = $id ? User::findOne($id) : (new User());
        if ($type != 1) {
            if (empty($id)) {
                throw new \Exception('The argument id is null.');
            }

            $epayuser = EpayUser::findOne(['appUserId' => $id]);
            $userBank = UserBanks::findOne(['uid' => $id]);
            $borrower = Borrower::findOne(['userId' => $id]);
            if (null === $borrower) {
                $borrower = new Borrower();
                $borrower->userId = $id;
            }

            if (!$epayuser) {
                throw new \Exception('Epayuser info is null.');
            }

            //若没有user-bank，则建立一个新的UserBank对象
            if (null !== $userBank) {
                $bankId = $userBank->bank_id;
            } else {
                $userBank = new UserBanks();
                $bankId = null;
            }
            $password = $model->password_hash;
            $model->scenario = 'add';
            $model->type = $type;
            $userBank->scenario = 'org_insert';

            $banks = Yii::$app->params['bank'];
            $bank = ['' => '--请选择--'];
            foreach ($banks as $key => $val) {
                $bank[$key] = $val['bankname'];
            }

            $borrowerType = $borrower->type;
            $cardNumber = $userBank->card_number;

            if ($model->load(Yii::$app->request->post())
                && $userBank->load(Yii::$app->request->post())
                && $borrower->load(Yii::$app->request->post())
                && $model->validate()
                && $userBank->validate()
                && $borrower->validate()
            ) {
                $data = Yii::$app->request->post();

                $model->safeMobile = SecurityUtils::encrypt($data['User']['mobile']);
                $model->safeIdCard = SecurityUtils::encrypt($data['User']['idcard']);

                if (!empty($model->password_hash)) {
                    $model->setPassword($model->password_hash);
                } else {
                    $model->password_hash = $password;
                }

                if ($bankId !== $userBank->bank_id) {
                    $userBank->bank_name = $bank[$userBank->bank_id];
                }

                $isPass = true;
                $hasLog = false;
                //融资会员类型、银行卡号改变进行验证
                if ($borrowerType != $borrower->type || $cardNumber != $userBank->card_number) {
                    $hasLog = (new Query())
                        ->select("b.id")
                        ->from("borrower as b")
                        ->innerJoin("user_bank as bank", "b.userId = bank.uid")
                        ->where(['b.type' => $borrower->type, 'bank.card_number' => $userBank->card_number])
                        ->one();
                }

                if (is_array($hasLog)) {
                    $userBank->addErrors(['card_number' => '该银行卡号已被占用']);
                    $isPass = false;
                }

                if ($isPass && $model->save(false) && $userBank->save(false) && $borrower->save(false)) {
                    $this->redirect(['/user/user/listr', 'type' => 2]);
                }
            }

            $model->password_hash = null;
        } else {
            $model->scenario = 'edit';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save(false)) {
                    $this->redirect(array('/user/user/' . ($type ? 'listt' : 'listr'), 'type' => $type));
                }
            }
        }

        return $this->render('edit', [
            'create_usercode' => $model->usercode,
            'category' => $type,
            'model' => $model,
            'epayuser' => $epayuser,
            'userBank' => $userBank,
            'bank' => $bank,
            'borrower' => $borrower,
        ]);
    }

    /**
     * 添加融资用户.
     */
    public function actionAdd()
    {
        $banks = Yii::$app->params['bank'];
        $bank = ['' => '--请选择--'];
        foreach ($banks as $key => $val) {
            $bank[$key] = $val['bankname'];
        }
        $userBank = new UserBanks();
        $userBank->scenario = 'org_insert';
        $epayuser = new EpayUser([
            'appUserId' => '0',
            'epayId' => 1,
            'clientIp' => ip2long(\Yii::$app->functions->getIp()),
            'regDate' => date('Y-m-d'),
            'createTime' => date('Y-m-d H:i:s'),
        ]);
        $borrower = new Borrower();

        $model = new User();
        $model->scenario = 'add';
        $model->type = 2;
        $model->usercode = User::create_code('usercode', Yii::$app->params['plat_code'] . 'QY', 6, 4);
        if ($model->load(Yii::$app->request->post())
            && $epayuser->load(Yii::$app->request->post())
            && $userBank->load(Yii::$app->request->post())
            && $borrower->load(Yii::$app->request->post())
            && $model->validate()
            && $epayuser->validate()
            && $userBank->validate()
            && $borrower->validate()
        ) {
            $model->safeMobile = SecurityUtils::encrypt($model->mobile);
            $isPass = true;
            if ($model->validate()) {
                $ump = Yii::$container->get('ump');
                $resp = $ump->getMerchantInfo($epayuser->epayUserId);
                if ($resp->isSuccessful()) {
                    if ('1' !== $resp->get('account_state')) {
                        $epayuser->addErrors(['epayUserId' => '联动商户账号状态不正确']);
                        $isPass = false;
                    }
                    //查询同账户类型、是否存在相同银行卡号，存在则提示银行卡信息错误
                    $hasLog = (new Query())
                        ->select("b.id")
                        ->from("borrower as b")
                        ->innerJoin("user_bank as bank", "b.userId = bank.uid")
                        ->where(['b.type' => $borrower->type, 'bank.card_number' => $userBank->card_number])
                        ->one();
                    if (is_array($hasLog)) {
                        $userBank->addErrors(['card_number' => '该银行卡号已被占用']);
                        $isPass = false;
                    }
                    if ($isPass) {
                        $transaction = Yii::$app->db->beginTransaction();
                        if (empty($model->password_hash)) {
                            throw new \Exception('The org_pass is null.');
                        }

                        $model->setPassword($model->password_hash);
                        $model->regContext = '';
                        if (!$model->save(false)) {
                            $transaction->rollBack();
                            $err = $model->getSingleError();
                            throw new \Exception($err['attribute'] . ': ' . $err['message']);
                        }

                        $epayuser->appUserId = strval($model->id);

                        if (!$epayuser->save(false)) {
                            $transaction->rollBack();
                            $err = $epayuser->getSingleError();
                            throw new \Exception($err['attribute'] . ': ' . $err['message']);
                        }

                        //添加一个融资会员的时候，同时生成对应的一条user_account记录
                        $userAccount = new UserAccount();
                        $userAccount->uid = $model->id;
                        $userAccount->type = UserAccount::TYPE_BORROW;

                        if (!$userAccount->save()) {
                            $transaction->rollBack();
                            $err = $userAccount->getSingleError();
                            throw new \Exception($err['attribute'] . ': ' . $err['message']);
                        }

                        //添加提现银行卡信息
                        $userBank->uid = $model->id;
                        $userBank->epayUserId = $epayuser->epayUserId;
                        $userBank->bank_name = $bank[$userBank->bank_id];
                        $userBank->binding_sn = TxUtils::generateSn('B');

                        if (!$userBank->save(false)) {
                            $transaction->rollBack();
                            $err = $userBank->getSingleError();
                            throw new \Exception($err['attribute'] . ': ' . $err['message']);
                        }

                        //添加融资会员附加信息
                        $borrower->userId = $model->id;
                        if (!$borrower->save(false)) {
                            $transaction->rollBack();
                            $err = $borrower->getSingleError();
                            throw new \Exception($err['attribute'] . ': ' . $err['message']);
                        }

                        $transaction->commit();
                        $this->redirect(['/user/user/listr', 'type' => 2]);
                    }
                } else {
                    $epayuser->addErrors(['epayUserId' => $resp->get('ret_msg')]);
                }
            }
        }

        if (empty($model->password_hash)) {
            $model->password_hash = \Yii::$app->functions->createRandomStr(8, 1);
        }

        return $this->render('edit', [
            'create_usercode' => $model->usercode,
            'category' => 2,
            'model' => $model,
            'epayuser' => $epayuser,
            'userBank' => $userBank,
            'bank' => $bank,
            'borrower' => $borrower,
        ]);
    }

    /**
     * 查询融资用户在联动的账户余额.
     */
    public function actionUmpOrgAccount($id)
    {
        $orgUser = $this->findOr404(User::class, ['id' => $id, 'type' => User::USER_TYPE_ORG]);
        $epayUser = $orgUser->epayUser;
        if (null === $epayUser) {
            throw $this->ex404('未找到第三方账户信息');
        }
        $epayUserId = $epayUser->epayUserId;
        $borrowerInfo = $orgUser->borrowerInfo;
        if (null === $borrowerInfo) {
            throw $this->ex404('未找到融资方账户类型信息');
        }

        $ump = Yii::$container->get('ump');
        if (2 === $borrowerInfo->type) {
            $resp = $ump->getUserInfo($epayUserId);
        } else {
            $resp = $ump->getMerchantInfo($epayUserId);
        }

        return [
            'balance' => StringUtils::amountFormat3(bcdiv($resp->get('balance'), 100, 2)),
        ];
    }

    /**
     * 导出投资人会员信息
     */
    public function actionLenderstats()
    {
        @ini_set('memory_limit', '256M');
        $where = [];
        if (Yii::$app->request->get('search')) {
            $users = (new UserSearch())->search(Yii::$app->request->get())->select('user.id')->asArray()->all();
            if (count($users) > 0) {
                $where = ['in', 'user.id', ArrayHelper::getColumn($users, 'id')];
            }
        }
        $data = UserStats::collectLenderData($where);
        UserStats::exportAsXlsx($data);
    }

    /**
     * 根据本地数据库用户名id查询联动信息
     */
    public function actionUmpuserinfo($uid)
    {
        //判断参数是否正确
        if (empty($uid)) {
            throw $this->ex404();
        }

        $user = User::findOne($uid);

        if (null === $user) {
            throw $this->ex404();
        }

        if (null !== $user->epayUser) {
            try {
                $epayuser = new EpayUser();
                $info = $epayuser->getUmpAccountStatus($user->epayUser);
                if (4 === $info['code']) {
                    $info['message'] = number_format($info['message'] / 100, 2);
                }
                return $info;
            } catch (\Exception $e) {
                return ['code' => -1, 'message' => $e->getMessage()];
            }
        }

        //返回状态-初始
        return ['code' => 0, 'message' => '初始'];
    }

    public function actionDrawStatsCount()
    {
        return ['small' => $this->DrawLimitCount(3), 'large' => $this->DrawLimitCount(5)];
    }

    public function actionDrawLimitList($times = 0)
    {
        $d = DrawRecord::tableName();
        $u = User::tableName();
        $ua = UserAccount::tableName();
        $query = (new Query())
            ->select("$u.id, count(*) as total, $u.usercode, $u.safeMobile, $u.real_name, from_unixtime($u.created_at) as createTime, $ua.available_balance")
            ->from($d)
            ->innerJoin($u, "$u.id = $d.uid")
            ->innerJoin($ua, "$ua.uid = $d.uid")
            ->where(["date_format(from_unixtime($d.created_at), '%Y%m')" => date('Ym')])
            ->andWhere(["$u.type" => User::USER_TYPE_PERSONAL])
            ->groupBy("$d.uid")
            ->having(['>=', 'total', $times]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $list = $query->offset($pages->offset)->limit($pages->limit);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $list->all(),
            'sort' => [
                'attributes' => ['usercode', 'mobile', 'real_name', 'createTime', 'available_balance'],
            ],
        ]);

        return $this->render('draw_list', ['dataProvider' => $dataProvider, 'pages' => $pages]);
    }

    /**
     * 更改user.regLocation.
     */
    public function actionUpdateRegLocation($user_id)
    {
        $user = $this->findOr404(User::class, $user_id);
        $user->scenario = 'updateRegLocation';

        if ($user->load(Yii::$app->request->post()) && $user->validate()) {
            $user->save(false);

            AdminLog::initNew($user)->save(false);

            return $this->redirect('/user/user/detail?id='.$user->id);
        }

        return $this->render('update_reg_location', ['user' => $user]);
    }

    private function DrawLimitCount($drawTimes)
    {
        $u = User::tableName();
        $d = DrawRecord::tableName();
        return (int)DrawRecord::find()
            ->select('count(*) as total')
            ->where(["date_format(from_unixtime($d.created_at), '%Y%m')" => date('Ym')])
            ->andWhere(["$u.type" => User::USER_TYPE_PERSONAL])
            ->innerJoin($u, "$u.id = $d.uid")
            ->groupBy('uid')
            ->having(['>=', 'total', $drawTimes])
            ->count();
    }

    /**
     * 用户登录日志
     */
    public function actionUserLog()
    {
        $request = Yii::$app->request->get();
        if (!empty($request['mobile'])) {
            $query = LoginLog::find()->andWhere([
                    'user_name' => $request['mobile'],
                    'type' => ['1', '2'],
                ]);
            $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
            $models = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();
        } else {
            $pages = new Pagination(['totalCount' => 0, 'pageSize' => '15']);
            $models = [];
        }
        return $this->render('userLog', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }
    /**
     * 受让人投资信息查询
     * 逻辑：根据转让挂单记录表credit_note的主键id，查询出该转让项目的受让人投资金额，在查询出受让人的个人信息
     * @param string id credit_note的主键id
     * @return string json(受让人手机号及投资金额) eg:json_encode(['mobile'=>'','principal'=>''])
     */
    public function actionInvestInfo()
    {
        $request = Yii::$app->request;
        $noteId  = (int)$request->post('id');
        $o = 'credit_order';
        $n = CreditNote::tableName();
        $creditNote = CreditNote::find()
            ->select("$o.user_id,$o.principal")
            ->where("$n.id = :noteId and $o.status = 1", ['noteId' => $noteId])
            ->innerJoin("$o", "$n.id = $o.note_id")
            ->asArray()
            ->all();
        $result = [];
        if (!empty($creditNote)) {
            $investInfo = User::find()->where(['in', 'id', ArrayHelper::getColumn($creditNote, 'user_id')])->all();
            $investInfo = ArrayHelper::index($investInfo, 'id');
            foreach ($creditNote as $key => $item) {
                $result[$key]['mobile']     = $investInfo[$item['user_id']]->getMobile();
                $result[$key]['principal']  = number_format($item['principal']/100, 2);
            }
        }
        return json_encode($result);
    }

    //融资用户信息导出
    public function actionOrgUserInfoExport()
    {
        $ids = Yii::$app->request->get('ids');

        $data = ['title' =>
            [
                '企业名称',
                '企业用户名',
                '账户类型',
                '企业密码',
                '联动用户ID号',
                '企业联系人',
                '开户行',
                '银行卡号',
            ],
        ];
        $u = User::tableName();
        $b = Borrower::tableName();
        $query = User::find()
            ->select("$u.id,$u.org_name,$u.username,$b.type,$u.real_name")
            ->innerJoinWith('borrowerInfo')
            ->where([
                "$u.type" => User::USER_TYPE_ORG,
                'is_soft_deleted' => 0,
            ]);

        if (!empty($ids)) {
            $query = $query->andWhere(['in', "$u.id", explode(',', $ids)]);
        }

        $users = $query->orderBy(["$u.id" => SORT_ASC])->all();

        if (0 !== count($users)) {
            $banks = Yii::$app->params['bank'];
            $account = Yii::$app->params['borrowerSubtype'];
            foreach ($banks as $key => $val) {
                $bank[$key] = $val['bankname'];
            }

            foreach ($users as $key => $user) {
                $epayuser = EpayUser::findOne(['appUserId' => $user->id]);
                $userBank = UserBanks::findOne(['uid' => $user->id]);

                $data[$key]['org_name'] = $user->org_name;
                $data[$key]['username'] = $user->username;
                $data[$key]['type'] = $account[$user->type];
                $data[$key]['password'] = '';
                $data[$key]['epayUserId'] = $epayuser->epayUserId;
                $data[$key]['real_name'] = $user->real_name;
                $data[$key]['bank_name'] = $bank[$userBank->bank_id];
                $data[$key]['card_number'] = $userBank->card_number;
            }
        }

        $path = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $fileName = 'org_user_info.xlsx';
        $file = $path . '/' . $fileName;
        if (file_exists($file)) {
            unlink($file);
        }

        $objPHPExcel = UserStats::initPhpExcelObject($data);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);

        if (file_exists($file)) {
            return Yii::$app->response->xSendFile('/downloads/' . $fileName, $fileName, [
                'xHeader' => 'X-Accel-Redirect',
            ]);
        }
    }

    /**
     *用户销户（锁定用户，禁止登录）
     */
    public function actionUserAccess()
    {
        $request = Yii::$app->request;
        $userId = (int)$request->get('id');
        $user = User::findOne($userId);
        if (null === $user) {
            throw new \Exception('未查询到该用户，请联系管理员');
        }
        $user->scenario = 'userAccess';
        if ($user->load($request->post()) && $user->validate() && $user->save(false)) {
            $this->redirect('/user/user/listt');
        }

        return $this->render('access', [
            'user' => $user,
        ]);
    }

    /**
     * 底层融资方列表.
     */
    public function actionListob()
    {
        $query = OriginalBorrower::find();

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();

        return $this->render('ob_list', [
            'model' => $model,
            'pages' => $pages,
        ]);
    }

    /**
     * 添加底层融资方.
     */
    public function actionAddob()
    {
        $ob = new OriginalBorrower();
        if ($ob->load(Yii::$app->request->post())
            && $ob->validate()
            && $ob->save()
        ) {
            $this->redirect('/user/user/listob');
        }

        return $this->render('ob_edit', ['ob' => $ob]);
    }

    /**
     * 编辑底层融资方.
     */
    public function actionEditob($id)
    {
        $ob = $this->findOr404(OriginalBorrower::className(), $id);
        if ($ob->load(Yii::$app->request->post())
            && $ob->validate()
            && $ob->save()
        ) {
            $this->redirect('/user/user/listob');
        }

        return $this->render('ob_edit', ['ob' => $ob]);
    }
}
