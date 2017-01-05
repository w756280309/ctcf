<?php

namespace frontend\modules\user\controllers;

use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\user\QpayBinding;
use common\models\user\UserAccount;
use common\service\BankService;
use Ding\DingNotify;
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
    * 未开户进入页面.
    *
    * 记录来源url(转让详情页/理财标的详情页)，用来跳回到详情页
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
           return $this->render('identity');
       } else {
           Yii::$app->session->set('to_url', '/user/userbank/identity');

           return $this->render('account', [
               'user' => $this->user,
           ]);
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
            Yii::$app->session->set('to_url', '/user/userbank/mybankcard');

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
        //保存目的地
        Yii::$app->session->set('to_url', '/user/userbank/recharge');
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
     * 我的银行卡
     */
    public function actionMybankcard()
    {
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if ($data['code']) {
            Yii::$app->session->set('to_url', '/user/userbank/mybankcard');

            return $this->redirect('/user/userbank/identity');
        }

        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE;
        $data = BankService::check($this->user, $cond);

        $user = $this->getAuthedUser();
        $user_bank = $user->qpay;
        $binding = null;
        $bankcardUpdate = null;

        if ($user_bank) {
            $bankcardUpdate = BankCardUpdate::find()
                ->where(['oldSn' => $user_bank->binding_sn, 'uid' => $user->id])
                ->orderBy('id desc')->one();
        } else {
            $binding = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);
        }

        return $this->render('mybank', [
            'user_bank' => $user_bank,
            'data' => $data,
            'binding' => $binding,
            'bankcardUpdate' => $bankcardUpdate,
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
            return $this->redirect('/user/userbank/mybankcard');
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if ($bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
            return $this->redirect('/user/userbank/mybankcard');
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
     * 检查银行卡号，返回开户行名称.
     */
    public function actionCheckbank($card)
    {
        return BankService::checkBankcard($card);
    }

    /**
     * 获取个人网银充值限额.
     */
    public function actionEbankLimit($bid)
    {
        $this->layout = false;

        return $this->render('ebank_limit', ['bid' => $bid]);
    }
}
