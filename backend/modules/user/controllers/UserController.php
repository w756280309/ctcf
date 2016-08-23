<?php

namespace backend\modules\user\controllers;

use Yii;
use backend\controllers\BaseController;
use common\models\user\User;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\Response;
use common\models\user\UserAccount;
use common\models\user\RechargeRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\epay\EpayUser;
use backend\modules\user\core\v1_0\UserAccountBackendCore;
use common\models\user\UserBanks;
use common\lib\user\UserStats;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

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

    public function userList($name = null, $mobile = null, $type = null)
    {
        if (empty($type) || !in_array($type, [1, 2])) {   //增加对type值的限定
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['TYPE参数错误'];
        }

        $query = User::find()->where(['user.type' => $type, 'is_soft_deleted' => 0]);
        //过滤 未投资时长
        $noInvestDays = intval(Yii::$app->request->get('noInvestDays'));
        if (!empty($noInvestDays)) {
            $date = date('Y-m-d', strtotime('- ' . $noInvestDays . ' day'));
            $query->leftJoin('user_info', 'user_info.user_id = user.id')->andFilterWhere(['<=', 'user_info.lastInvestDate', $date]);
        }
        //过滤有余额未投资
        $noInvest = boolval(Yii::$app->request->get('noInvest'));
        if ($noInvest) {
            $query->leftJoin('user_info', 'user_info.user_id = user.id')->andFilterWhere(['isInvested' => 0])->leftJoin('user_account', 'user_account.uid = user.id')->andFilterWhere(['>', 'available_balance', 0]);
        }
        if ($type == User::USER_TYPE_PERSONAL) {
            $query->with('lendAccount');
            if (!empty($name)) {
                $query->andFilterWhere(['like', 'real_name', $name]);
            }
            if (!empty($mobile)) {
                $query->andFilterWhere(['like', 'mobile', $mobile]);
            }
        } else {
            $query->with('borrowAccount');
            if (!empty($name)) {
                $query->andFilterWhere(['like', 'org_name', $name]);
            }
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('created_at desc')->all();

        return $this->render('list', [
                'model' => $model,
                'category' => $type,
                'pages' => $pages,
        ]);
    }

    /**
     * 投资人.
     *
     * @param type $name
     * @param type $mobile
     * @param type $type
     *
     * @return type
     */
    public function actionListt($name = null, $mobile = null, $type = 1)
    {
        return $this->userList($name, $mobile, $type);
    }

    /**
     * 融资人.
     *
     * @param type $name
     * @param type $mobile
     * @param type $type
     *
     * @return type
     */
    public function actionListr($name = null, $mobile = null, $type = 2)
    {
        return $this->userList($name, $mobile, $type);
    }

    /**
     * 查看用户详情.
     */
    public function actionDetail($id, $type)
    {
        if (empty($id) || empty($type) || !in_array($type, [1, 2])) {
            throw new NotFoundHttpException();     //参数无效,抛出404异常
        }

        $userInfo = User::findOne($id);
        if (null === $userInfo) {
            throw new NotFoundHttpException();     //对象为空时,抛出404异常
        }

        $uabc = new UserAccountBackendCore();
        $recharge = $uabc->getRechargeSuccess($id);
        $draw = $uabc->getDrawSuccess($id);

        if (User::USER_TYPE_PERSONAL === (int) $type) {
            $rcMax = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES, 'uid' => $id])->max('updated_at');
            $order = $uabc->getOrderSuccess($id);
            $product = $ret = ['count' => 0, 'sum' => 0];
            $ua = $userInfo->lendAccount;    //获取投资用户账户信息
        } else {
            $rcMax = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id])->min('start_date');
            $ret = $uabc->getReturnInfo($id);
            $product = $uabc->getProduct($id);
            $order = ['count' => 0, 'sum' => 0];
            $ua = $userInfo->borrowAccount;  //获取融资用户账户信息
        }

        $tztimeMax = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS, 'uid' => $id])->max('updated_at');
        $bc = new \common\lib\bchelp\BcRound();
        $userYuE = $bc->bcround(bcadd($ua['available_balance'], $ua['freeze_balance']), 2);

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
            throw new NotFoundHttpException();
        }

        $user = User::findOne($uid);

        if (null === $user) {
            throw new NotFoundHttpException();
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