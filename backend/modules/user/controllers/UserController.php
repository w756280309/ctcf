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
use backend\modules\user\core\v1_0\UserAccountBackendCore;

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
                ], parent::behaviors());

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

   //查看用户详情
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
        $userLiCai = $ua->investment_balance;//理财金额
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

    //添加新融资客户
    public function actionEdit($id = null, $type = 2)
    {
        $model = $id ? User::findOne($id) : (new User());
        if ($type != 1) {
            //添加
            $model->scenario = 'add';
            $model->type = $type;
            if (empty($id)) {
                $model->usercode = User::create_code('usercode', 'WDJFQY', 6, 4);
                if (!empty(Yii::$app->params[org_pass])) {
                    $model->setPassword(Yii::$app->params[org_pass]);
                } else {
                    throw new Exception('The org_pass is null.');
                }
            }
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save()) {
                    if (empty($id)) {
                        //添加一个融资会员的时候，同时生成对应的一条user_account记录
                        $userAccount = new UserAccount();
                        $userAccount->uid = $model->id;
                        $userAccount->type = UserAccount::TYPE_BORROW;
                        $userAccount->save();
                    }
                    $this->redirect(array('/user/user/listr', 'type' => 2));
                } else {
                    $this->alert = 1;
                    $this->toUrl = 'edit';
                }
            }
        } else {
            $model->scenario = 'edit';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save()) {
                    $this->redirect(array('/user/user/'.($type ? 'listt' : 'listr'), 'type' => $type));
                }
                $this->alert = 1;
                $this->toUrl = 'edit';
            }
        }

        return $this->render('edit', [
                'create_usercode' => $model->usercode,
                'category' => $type,
                'model' => $model,
        ]);
    }
}
