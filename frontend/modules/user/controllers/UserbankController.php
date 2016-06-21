<?php

namespace frontend\modules\user\controllers;

use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\user\UserAccount;
use common\service\BankService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;

class UserbankController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    //未开户进入页面
   public function actionIdentity(){
        $this->layout = 'main';
        return $this->render('identity');
   }

    /**
     * 实名认证表单页.
     */
    public function actionIdcardrz()
    {
        $this->layout = 'main';

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_Y;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isPost) {
                return $data;
            } else {
                return $this->redirect('/user/user/index');
            }
        }
        if (Yii::$app->request->isPost) {
            $model = $this->getAuthedUser();
            $model->scenario = 'idcardrz';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $umpService = new \common\service\UmpService();
                try {
                    $umpService->register($model);
                    return ['tourl' => '/info/success?source=tuoguan&jumpUrl=/user/qpay/binding/umpmianmi', 'code' => 0, 'message' => '您已成功开户'];
                } catch (\Exception $ex) {
                    return ['code' => 1, 'message' => $ex->getMessage()];
                }
            } else {
                $err = $model->getSingleError();
                return ['code' => 1, 'message' => $err['message']];
            }
        } else {
            return $this->render('idcardrz', []);
        }
    }

    /**
     * 绑定银行卡.
     * 先决条件:
     * 1. 实名认证
     * 2. 开通免密
     * 3. 未绑卡
     */
    public function actionBindbank()
    {
        //检查是否已绑卡
        $cond = 0 | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->redirect('/user/userbank/mybankcard');
        }

        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->redirect('/user/userbank/identity');
        }
        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE;
        $data = BankService::check($this->user, $cond);

        $banks = BankManager::getQpayBanks();

        return $this->render('bindbank', [
            'banklist' => $banks,
            'data' => $data,
        ]);
    }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        $this->layout = 'main';
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->redirect('/user/userbank/identity');
        }
        $user = $this->getAuthedUser();
        $uid = $user->id;
        $user_bank = $user->qpay;
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();
        $bank = QpayConfig::findOne($user_bank->bank_id);
        //检查用户是否完成快捷支付
        $data = BankService::checkKuaijie($user);
        if ($data['code'] == 1 && \Yii::$app->request->isAjax) {
            return ['next' => $data['tourl']];
        }

        return $this->render('recharge', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'data' => $data,
            'bank' => $bank
        ]);
    }

    /**
     * 我的银行卡
     */
    public function actionMybankcard()
    {
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->redirect('/user/userbank/identity');
        }
        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE;
        $data = BankService::check($this->user, $cond);

        $this->layout = 'main';
        $user = $this->getAuthedUser();
        $user_bank = $user->qpay;
        return $this->render('mybank', [
            'user_bank' => $user_bank,
            'data' => $data,
        ]);
    }
}

