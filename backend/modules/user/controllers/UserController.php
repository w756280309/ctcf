<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use backend\modules\user\core\v1_0\UserAccountBackendCore;
use common\lib\err\Err;
use common\lib\user\UserStats;
use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\bank\Bank;
use common\models\epay\EpayUser;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\promo\InviteRecord;
use common\models\user\CoinsRecord;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\UserSearch;
use common\models\user\DrawRecord;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use wap\modules\promotion\models\RankingPromo;
use Wcg\Http\HeaderUtils;
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
    public function actionPointList($userId)
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
        $type_sql = "SELECT distinct(`ref_type`) FROM `point_record`";
        $db = Yii::$app->db;
        $type = $db->createCommand($type_sql)->queryAll();

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
            'types' => $type,
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
        if ( is_dir($path)) {
            $handle = opendir( $path );
            if ($handle) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        if (false !== strpos($item, '投资用户信息') ) {
                            $fileName = $path . '/'. $item;
                            header(HeaderUtils::getContentDispositionHeader($item, \Yii::$app->request->userAgent));
                            readfile($fileName);
                            exit();
                        }
                    }
                }
            }
            closedir( $handle );
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

        $query = User::find()
            ->with('borrowAccount')
            ->where([
                'user.type' => User::USER_TYPE_ORG,
                'is_soft_deleted' => 0,
            ]);

        if (isset($request['name']) && !empty($request['name'])) {
            $query->andFilterWhere(['like', 'org_name', $request['name']]);
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('created_at desc')->all();

        return $this->render('list', [
            'model' => $model,
            'category' => User::USER_TYPE_ORG,
            'pages' => $pages,
            'request' => $request,
        ]);
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
    public function actionDetail($id)
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
            return $this->normalUserDetail($user);
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
        ]);
    }

    /**
     * 普通会员详情
     * @param User $user
     */
    private function normalUserDetail(User $user)
    {
        $id = $user->id;
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
        } else {
            $loan = [];
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

            if (!$epayuser) {
                throw new \Exception('Epayuser info is null.');
            }

            $password = $model->password_hash;
            $bankId = $userBank->bank_id;

            $model->scenario = 'add';
            $model->type = $type;

            $userBank->scenario = 'org_insert';

            $banks = Yii::$app->params['bank'];
            $bank = ['' => '--请选择--'];
            foreach ($banks as $key => $val) {
                $bank[$key] = $val['bankname'];
            }

            if ($model->load(Yii::$app->request->post())
                && $userBank->load(Yii::$app->request->post())
                && $model->validate()
                && $userBank->validate()
            ) {
                if (!empty($model->password_hash)) {
                    $model->setPassword($model->password_hash);
                } else {
                    $model->password_hash = $password;
                }

                if ($bankId !== $userBank->bank_id) {
                    $userBank->bank_name = $bank[$userBank->bank_id];
                }

                if ($model->save(false) && $userBank->save(false)) {
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

        $model = new User();
        $model->scenario = 'add';
        $model->type = 2;
        $model->usercode = User::create_code('usercode', 'WDJFQY', 6, 4);
        if ($model->load(Yii::$app->request->post())
            && $epayuser->load(Yii::$app->request->post())
            && $userBank->load(Yii::$app->request->post())
            && $model->validate()
            && $epayuser->validate()
            && $userBank->validate()
        ) {
            $ump = Yii::$container->get('ump');
            $resp = $ump->getMerchantInfo($epayuser->epayUserId);
            if ($resp->isSuccessful()) {
                if ('1' === $resp->get('account_state')) {
                    $epayuser->addErrors(['epayUserId' => '联动商户账号状态不正确']);
                }

                $transaction = Yii::$app->db->beginTransaction();
                if (empty($model->password_hash)) {
                    throw new \Exception('The org_pass is null.');
                }

                $model->setPassword($model->password_hash);
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

                if (!$userBank->save(false)) {
                    $transaction->rollBack();
                    $err = $userBank->getSingleError();
                    throw new \Exception($err['attribute'] . ': ' . $err['message']);
                }

                $transaction->commit();
                $this->redirect(['/user/user/listr', 'type' => 2]);
            } else {
                $epayuser->addErrors(['epayUserId' => $resp->get('ret_msg')]);
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
        ]);
    }

    /**
     * 查询融资用户在联动的账户余额.
     */
    public function actionUmpOrgAccount($id)
    {
        $orgUser = $this->findOr404(User::class, ['id' => $id, 'type' => User::USER_TYPE_ORG]);
        $epayUserId = $orgUser->epayUser->epayUserId;

        $resp = \Yii::$container->get('ump')->getMerchantInfo($epayUserId);

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
            ->select("$u.id, count(*) as total, $u.usercode, $u.mobile, $u.real_name, from_unixtime($u.created_at) as createTime, $ua.available_balance")
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
}
