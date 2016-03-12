<?php

namespace app\modules\user\controllers;

use frontend\controllers\BaseController;
use common\models\user\UserAccount;
use common\service\BankService;

class UseraccountController extends BaseController
{
    public $layout = '@app/views/layouts/footer';

    /**
     * 账户中心展示页.
     */
    public function actionAccountcenter()
    {
        $uid = $this->user->id;
        $check_arr = BankService::checkKuaijie($this->user);

        if ($check_arr['code'] === 1) {
            $errflag = 1;
            $errmess = $check_arr['message'];
        }

        $account = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $uid]);

        return $this->render('accountcenter', ['model' => $account, 'username' => $this->user->real_name, 'errflag' => $errflag, 'errmess' => $errmess]);
    }

    /**
     * 充值前校验用户是否开通联动账户
     */
    public function actionRechargeValidate()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;

        return BankService::check($this->user, $cond);
    }

    /**
     * 提现前校验用户是否开通联动账户并绑定快捷卡
     */
    public function actionDrawValidate()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;

        return BankService::check($this->user, $cond);
    }
}
