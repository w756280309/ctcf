<?php

namespace app\modules\user\controllers;

use Ding\DingNotify;
use Yii;
use app\controllers\BaseController;
use common\models\user\DrawRecord;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\service\BankService;
use common\service\UmpService;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\bank\BankCardUpdate;

class UserbankController extends BaseController
{
    /**
     * 修改交易密码表单页.
     */
    public function actionEditbuspass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if (1 === $data['code']) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('editbuspass', ['data' => $data]);
            }
        }

        return $this->render('editbuspass', ['data' => $data]);
    }

    public function actionResetTradePass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if (1 === $data['code']) {
            return $data;
        }

        $ump = Yii::$container->get('ump');

        $resp = $ump->resetTradePass($this->getAuthedUser());

        if ($resp->isSuccessful()) {
            return ['code' => 0, 'message' => '重置后的密码已经发送到您的手机'];
        } else {
            \Yii::trace('【重置交易密码】'.$this->getAuthedUser()->idcard.':'.$resp->get('ret_code').':'.$resp->get('ret_msg'), 'umplog');

            return ['code' => 1, 'message' => '当前网络异常，请稍后重试'];
        }
    }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        \Yii::$app->session->remove('cfca_qpay_recharge');
        \Yii::$app->session->remove('recharge_back_url');
        $user = $this->getAuthedUser();

        //检查用户是否完成快捷支付
        $data = BankService::checkKuaijie($user);
        if ($data['code'] == 1 && \Yii::$app->request->isAjax) {
            return ['next' => $data['tourl']];
        }

        $user_bank = UserBanks::find()->where(['uid' => $user->id])->select('id,binding_sn,bank_id,bank_name,card_number')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $user->id])->select('id,uid,in_sum,available_balance')->one();
        $bank = QpayConfig::findOne($user_bank->bank_id);

        //保存充值来源
        if ($from = Yii::$app->request->get('from')) {
            \Yii::$app->session['recharge_from_url'] = urldecode($from);
        }
        if ($to = Yii::$app->request->get('backUrl')) {
            \Yii::$app->session['recharge_back_url'] = $to;
        }

        return $this->render('recharge', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data, 'bank' => $bank]);
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $user = $this->getAuthedUser();
        $uid = $user->id;

        $user_acount = $user->lendAccount;
        $user_bank = $user->qpay;

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;
        $data = BankService::check($user, $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
            }
        }

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            try {
                $drawFee = $user->getDrawCount() >= Yii::$app->params['draw_free_limit'] ? Yii::$app->params['drawFee'] : 0;
                $drawres = DrawManager::initDraw($user_acount, $draw->money, $drawFee);
                $option = array();
                if (null != Yii::$app->request->get('token')) {
                    $option['app_token'] = Yii::$app->request->get('token');
                }
                $next = Yii::$container->get('ump')->initDraw($drawres, null, $option);

                return ['code' => 0, 'message' => '', 'tourl' => $next];
            } catch (DrawException $ex) {
                if (DrawException::ERROR_CODE_ENOUGH === $ex->getCode()) {
                    return ['code' => 1, 'message' => '您的账户余额不足,仅可提现'.$ex->getMessage().'元', 'money' => $ex->getMessage()];
                } else {
                    return ['code' => 1, 'message' => $ex->getMessage()];
                }
            } catch (\Exception $ex) {
                $draw->addError('money', $ex->getMessage());
            }
        }

        if ($draw->getErrors()) {
            $message = $draw->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('tixian', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'data' => [
                'code' => 0,
                'message' => '',
                'tourl' => '',
            ],
        ]);
    }

    /**
     * 银行限额显示.
     */
    public function actionBankxiane()
    {
        $qpayBanks = QpayConfig::find()->all();

        return $this->render('bankxiane', ['banks' => $qpayBanks]);
    }

    /**
     * 绑卡受理结果页面.
     *
     * @param $ret success/error
     */
    public function actionAccept($ret = 'error')
    {
        return $this->render('acceptres', ['ret' => $ret]);
    }

    /**
     * 快捷充值结果页面.
     *
     * @param type $ret
     */
    public function actionQpayres($ret = 'error')
    {
        $from_url = '';
        if (!Yii::$app->user->isGuest && isset(Yii::$app->session['recharge_from_url'])) {
            $from_url = Yii::$app->session['recharge_from_url'];
            unset(Yii::$app->session['recharge_from_url']);
        }

        return $this->render('qpayres', ['ret' => $ret, 'from_url' => $from_url]);
    }

    /**
     * 提现结果页.
     *
     * @param type $ret
     */
    public function actionDrawres($ret = 'error')
    {
        return $this->render('drawres', ['ret' => $ret]);
    }

    /**
     * 开户结果页.
     *
     * @param type $ret
     */
    public function actionRzres($ret = 'error')
    {
        return $this->render('rzres', ['ret' => $ret]);
    }

    /**
     * 换卡申请页面.
     */
    public function actionUpdatecard()
    {
        $user = $this->getAuthedUser();

        $data = BankService::checkKuaijie($user);
        if (1 === $data['code']) {
            return $this->redirect('/user/bank/card');
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if (null !== $bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
            return $this->redirect('/user/bank/card');
        }

        $banks = BankManager::getQpayBanks();

        return $this->render('updatecard', ['banklist' => $banks]);
    }

    /**
     * 换卡申请结果页面.
     */
    public function actionUpdatecardnotify($ret = 'error')
    {
        return $this->render('updatecardnotify', ['ret' => $ret]);
    }
}
