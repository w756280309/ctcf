<?php

namespace frontend\modules\user\controllers;

use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\user\QpayBinding;
use common\models\user\UserAccount;
use common\models\user\UserFreepwdRecord;
use common\service\BankService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;

class UserbankController extends BaseController
{
    public $layout = 'main';

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

   /**
    * 资金托管账户.
    *
    * 记录来源url(转让详情页/理财标的详情页)，用来跳回到详情页.
    */
   public function actionIdentity()
   {
       //检查是否开户
       $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
       $data = BankService::check($this->getAuthedUser(), $cond);

       if ($data['code']) {
           $from = Yii::$app->request->get('from');
           if ($from && filter_var($from, FILTER_VALIDATE_URL)) {
               Yii::$app->session->set('to_url', $from);
           }
           return $this->render('identity', [
               'title' => '资金托管账户',
           ]);
       } else {
           Yii::$app->session->set('to_url', '/user/userbank/identity');

           return $this->render('account', [
               'user' => $this->user,
           ]);
       }
   }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        //保存目的地
        Yii::$app->session->set('to_url', '/user/userbank/recharge');
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            return $this->render('identity', [
                'title' => '快捷充值',
            ]);
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
        $binding = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);

        return $this->render('recharge', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'data' => $data,
            'bank' => $bank,
            'user' => $user,
            'binding' => $binding,
        ]);
    }


    /**
     * 更换银行卡.
     */
    public function actionUpdatecard()
    {
        $user = $this->getAuthedUser();

        $data = BankService::checkKuaijie($user);
        if ($data['code']) {
            return $this->redirect('/user/bank/card');
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if ($bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
            return $this->redirect('/user/bank/card');
        }

        $banks = BankManager::getQpayBanks();

        return $this->render('updatecard', ['banklist' => $banks]);
    }

    /**
     * 银行限额.
     * 包括快捷充值限额以及网银充值限额.
     */
    public function actionXiane()
    {
        $qpayBanks = QpayConfig::find()->all();

        return $this->render('xiane', ['qpay' => $qpayBanks]);
    }

    /**
     * 获取个人网银充值限额.
     */
    public function actionEbankLimit($bid)
    {
        $this->layout = false;

        return $this->render('ebank_limit', ['bid' => $bid]);
    }

    /**
     * 快捷充值商业委托
    */
    public function actionRechargeDepute(){
        //保存目的地
        Yii::$app->session->set('to_url', '/user/userbank/recharge-depute');
        //检查是否开户
        $user = $this->getAuthedUser();
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if ($data['code']) {
            return $this->render('identity', [
                'title' => '快捷充值(商业委托)',
            ]);
        }

        $user_bank = $user->qpay;
        //检查用户是否开通商业委托免密协议
        $userfree = UserFreepwdRecord::find()
            ->where(['uid' => $user->id])
            ->orderBy('status desc')->one();
        $toOpenMm = [];
        if($user_bank){
            if(empty($userfree)){//未开通
                $toOpenMm = UserFreepwdRecord::getCheckStatusInfo()[0];
            }else if(UserFreepwdRecord::OPEN_FREE_RECHARGE_PASS !== UserFreepwdRecord::getCheckStatusInfo()[$userfree->status]['code']){
                $toOpenMm  = UserFreepwdRecord::getCheckStatusInfo()[$userfree->status];
            }
        }

        $bank = QpayConfig::findOne($user_bank->bank_id);
        $binding = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);
        return $this->render('recharge-depute', [
            'user_bank' => $user_bank,
            'data' => $data,
            'bank' => $bank,
            'user' => $user,
            'binding' => $binding,
            'toOpenMm' => $toOpenMm,
        ]);
    }

    /**
     * 开通快捷支付（商业委托）
     * */
    public function  actionFastpay(){
        $user = $this->getAuthedUser();
        if(null === $user){
            $this->redirect('/site/login');
        }
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if ($data['code']) {
            return $this->render('identity', [
                'title' => '快捷充值(商业委托)',
            ]);
        }
        $epayUserId = $user->epayUser->epayUserId;
        $oneinfo = UserFreepwdRecord::findOne(['uid'=> $user->id]);
        if(empty($oneinfo)){
            $model = new UserFreepwdRecord();
            $model->uid = $user->id;
            $model->status = UserFreepwdRecord::OPEN_FREE_STATUS_WAIT;
            $model->epayUserId = $epayUserId;
            $model->save(false);
        }
        $this->redirect(Yii::$container->get('ump')->openFastPay($epayUserId, CLIENT_TYPE));
    }

    /**
     * 开通免密充值（商业委托）
     * */
    public function  actionFreeRecharge(){
        $user = $this->getAuthedUser();
        if(null === $user){
            $this->redirect('/site/login');
        }
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if ($data['code']) {
            return $this->render('identity', [
                'title' => '快捷充值(商业委托)',
            ]);
        }
        $this->redirect(Yii::$container->get('ump')->openFreeRecharge($user->epayUser->epayUserId, CLIENT_TYPE));
    }
}
