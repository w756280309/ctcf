<?php

namespace app\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\user\EditpassForm;
use common\models\user\UserAccount;
use common\service\BankService;
use common\models\city\Region;
use common\models\draw\Draw;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;

class UseraccountController extends BaseController
{
    public $layout = '@app/views/layouts/main';

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

    /**
     * 提现页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $user_bank = $this->user->qpay;
        $user_acount = $this->user->lendAccount;
        $province = Region::find()->where(['province_id' => 0])->select('id,name')->asArray()->all();

        $check_arr = BankService::checkKuaijie($user);
        if ($check_arr[code] === 1) {
            return $this->goHome();
        }

        $model = new EditpassForm();
        $draw = new Draw();
        $model->scenario = 'checktradepwd';
        if ($draw->load(Yii::$app->request->post()) && $draw->validate() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                DrawManager::init($user, $draw->money, \Yii::$app->params['drawFee']);

                return $this->redirect('/user/useraccount/tixianback?flag=succ');
            } catch (DrawException $ex) {
                $draw->addError('money', $ex->getMessage());
            }
        }
        return $this->render('tixian', ['model' => $model, 'bank' => $user_bank, 'user_account' => $user_acount, 'draw' => $draw, 'province' => $province]);
    }

    /**
     * 提现返回页面.
     */
    public function actionTixianback($flag)
    {
        if (!in_array($flag, ['err', 'succ'])) {
            exit('参数错误');
        }

        return $this->render('tixianback', ['flag' => $flag]);
    }
}
