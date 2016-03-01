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
        if (empty($type)) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['TYPE参数错误'];
        }

        $query = User::find()->where(['type' => $type]);
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
    public function actionDetail($id = null, $type = null)
    {
        if ($type == User::USER_TYPE_PERSONAL) {
            $select = 'usercode,real_name,mobile,created_at,idcard,updated_at,last_login,login_from,idcard_status';
        } else {
            $select = 'usercode,org_name,tel,created_at,real_name,idcard,law_mobile,mobile,law_master,law_master_idcard,business_licence,org_code,shui_code,updated_at';
        }
        $userInfo = User::find()->where(['id' => $id])->select($select)->one();

        $uabc = new UserAccountBackendCore();
        $recharge = $uabc->getRechargeSuccess($id);
        $draw = $uabc->getDrawSuccess($id);

        $ua = $uabc->getUserAccount($id);
        $userLiCai = $ua->investment_balance; //理财金额
        if (Yii::$app->request->get('type') == User::USER_TYPE_PERSONAL) {
            $rcMax = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES, 'uid' => $id])->max('updated_at');
            $order = $uabc->getOrderSuccess($id);
            $product = $ret = ['count' => 0, 'sum' => 0];
        } else {
            $rcMax = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id])->min('start_date');
            $ret = $uabc->getReturnInfo($id);
            $product = $uabc->getProduct($id);
            $order = ['count' => 0, 'sum' => 0];
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
                'userLiCai' => $userLiCai,
                'tzTime' => $tztimeMax,
                'tzNum' => $order['count'],
                'tzMoneyTotal' => $order['sum'],
                'rzNum' => $product['count'],
                'rzMoneyTotal' => $product['sum'],
                'ret' => $ret,
                'userinfo' => $userInfo,
                'id' => $id,
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
}
