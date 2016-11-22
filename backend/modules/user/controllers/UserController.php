<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\lib\user\UserStats;
use common\models\affiliation\UserAffiliation;
use common\models\epay\EpayUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use backend\modules\user\core\v1_0\UserAccountBackendCore;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\UserSearch;
use Yii;
use yii\data\Pagination;
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
     * 投资人列表
     */
    public function actionListt()
    {
        $userSearch = new UserSearch();
        $query = $userSearch->search(Yii::$app->request->get());
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 15,
        ]);
        $user = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['user.created_at' => SORT_DESC])->all();

        return $this->render('list', [
            'model' => $user,
            'pages' => $pages,
            'category' => User::USER_TYPE_PERSONAL,
        ]);
    }

    /**
     * 融资人列表
     */
    public function actionListr()
    {
        $query = User::find()->where(['user.type' => User::USER_TYPE_ORG, 'is_soft_deleted' => 0]);
        $query->with('borrowAccount');
        $name  = Yii::$app->request->get('name');
        if (!empty($name)) {
            $query->andFilterWhere(['like', 'org_name', $name]);
        }
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('created_at desc')->all();

        return $this->render('list', [
            'model' => $model,
            'category' => User::USER_TYPE_ORG,
            'pages' => $pages,
        ]);
    }

    /**
     * 查看用户详情.
     */
    public function actionDetail($id, $type)
    {
        if (empty($id) || empty($type) || !in_array($type, [1, 2])) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $userInfo = User::findOne($id);
        if (null === $userInfo) {
            throw $this->ex404();     //对象为空时,抛出404异常
        }

        $uabc = new UserAccountBackendCore();
        $recharge = $uabc->getRechargeSuccess($id);
        $draw = $uabc->getDrawSuccess($id);

        if (User::USER_TYPE_PERSONAL === (int) $type) {
            $rcMax = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES, 'uid' => $id])->max('updated_at');
            $order = $uabc->getOrderSuccess($id);
            $product = $ret = ['count' => 0, 'sum' => 0];
            $ua = $userInfo->lendAccount;    //获取投资用户账户信息
            $userAff = UserAffiliation::findOne(['user_id' => $userInfo->id]);
            $txRes = Yii::$container->get('txClient')->get('credit-order/records', [
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

        } else {
            $rcMax = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id])->min('start_date');
            $ret = $uabc->getReturnInfo($id);
            $product = $uabc->getProduct($id);
            $order = ['count' => 0, 'sum' => 0, 'creditSuccessCount' => 0, 'creditTotalAmount' => 0, 'latestOrderTime' => ''];
            $ua = $userInfo->borrowAccount;  //获取融资用户账户信息
            $userAff = null;
            $leiji = '0.00';
        }

        $tztimeMax = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS, 'uid' => $id])->max('updated_at');
        $userYuE = $ua['available_balance'];

        return $this->render('detail', [
            'czTime' => $rcMax,
            'czNum' => $recharge['count'],
            'czMoneyTotal' => $recharge['sum'],
            'txNum' => $draw['count'],
            'txMoneyTotal' => $draw['sum'],
            'userYuE' => $userYuE,
            'userLiCai' => $ua->investment_balance,
            'tzTime' => $tztimeMax,
            'tzNum' => $order['count'],
            'tzMoneyTotal' => $order['sum'],
            'rzNum' => $product['count'],
            'rzMoneyTotal' => $product['sum'],
            'ret' => $ret,
            'userinfo' => $userInfo,
            'userAff' => $userAff,
            'creditSuccessCount' => $order['creditSuccessCount'],
            'creditTotalAmount' => $order['creditTotalAmount'],
            'latestCreditOrderTime' => $order['latestCreditOrderTime'],
            'leiji' => $leiji > 0 ? $leiji : '0.00',
        ]);
    }

    /**
     * 查看指定用户的债权投资明细
     * @param $id
     * @return Response
     */
    public function actionCreditRecords($id, $type)
    {
        $user = User::findOne($id);
        $txRes = Yii::$container->get('txClient')->get('credit-order/records', [
            'user_id' => $id,
            'page' => Yii::$app->request->get('page'),
            'page_size' => Yii::$app->request->get('page_size'),
        ]);
        $loan = OnlineProduct::find()->where(['in', 'id', ArrayHelper::getColumn($txRes['data'], 'loan_id')])->all();
        $loan = ArrayHelper::index($loan, 'id');
        $pages = new Pagination(['totalCount' => $txRes['totalCount'], 'pageSize' => 10]);
        return $this->render('credit_records', [
            'username' => $user->real_name,
            'txRes' => $txRes,
            'pages' => $pages,
            'loan' => $loan,
            'id' => $id,
            'type' => $type,
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
                && $userBank->validate()) {
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
                    $this->redirect(array('/user/user/'.($type ? 'listt' : 'listr'), 'type' => $type));
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
            && $userBank->validate()) {
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
                    throw new \Exception($err['attribute'].': '.$err['message']);
                }

                $epayuser->appUserId = strval($model->id);

                if (!$epayuser->save(false)) {
                    $transaction->rollBack();
                    $err = $epayuser->getSingleError();
                    throw new \Exception($err['attribute'].': '.$err['message']);
                }

                //添加一个融资会员的时候，同时生成对应的一条user_account记录
                $userAccount = new UserAccount();
                $userAccount->uid = $model->id;
                $userAccount->type = UserAccount::TYPE_BORROW;

                if (!$userAccount->save()) {
                    $transaction->rollBack();
                    $err = $userAccount->getSingleError();
                    throw new \Exception($err['attribute'].': '.$err['message']);
                }

                //添加提现银行卡信息
                $userBank->uid = $model->id;
                $userBank->epayUserId = $epayuser->epayUserId;
                $userBank->bank_name = $bank[$userBank->bank_id];

                if (!$userBank->save(false)) {
                    $transaction->rollBack();
                    $err = $userBank->getSingleError();
                    throw new \Exception($err['attribute'].': '.$err['message']);
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
     * 导出投资人会员信息
     */
    public function actionLenderstats()
    {
        $data = UserStats::collectLenderData();
        UserStats::createCsvFile($data);
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
                    $info['message'] = number_format($info['message']/100, 2);
                }
                return $info;
            } catch(\Exception $e) {
                return ['code' => -1, 'message'=>$e->getMessage()];
            }
        }

        //返回状态-初始
        return ['code'=>0, 'message'=>'初始'];
    }
}