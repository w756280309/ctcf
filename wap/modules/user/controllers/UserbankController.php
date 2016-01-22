<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\models\city\Region;
use common\models\user\DrawRecord;
use common\models\user\EditpassForm;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\service\BankService;
use common\service\SmsService;
use common\service\UserService;
use Yii;
use yii\web\Response;

class UserbankController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->layout = '@app/modules/order/views/layouts/buy';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 实名认证表单页.
     */
    public function actionIdcardrz()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_Y;
        $data = BankService::check($this->user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('idcardrz', $data);
            }
        }

        $model = $this->user;
        $model->scenario = 'idcardrz';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->idcard_status = User::IDCARD_STATUS_PASS;
            if ($model->save()) {
                //实名认证成功
                return ['tourl' => '/user/userbank/bindbank', 'code' => 0, 'message' => '实名认证成功'];
            }
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('idcardrz');
    }

    /**
     * 绑定银行卡表单页.
     */
    public function actionBindbank()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($this->user, $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                $arr = array();

                return $this->render('bindbank', ['banklist' => $arr, 'data' => $data]);
            }
        }

        $user = $this->user;
        $model = new UserBanks();
        $model->scenario = 'step_first';
        $model->uid = $user->id;
        $model->account = $user->real_name;
        $model->account_type = UserBanks::PERSONAL_ACCOUNT;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = UserBanks::STATUS_YES;
            if ($model->save()) {
                //绑卡成功
                $res = SmsService::editSms($user->id);

                return ['tourl' => '/user/userbank/addbuspass', 'code' => 1, 'message' => '绑卡成功'];
            }
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        $arr = array();
        $bank = Yii::$app->params['bank'];
        $i = 0;
        foreach ($bank as $key => $val) {
            $arr[$i] = array('id' => $key, 'bankname' => $val['bankname'], 'image' => $val['image']);
            ++$i;
        }

        return $this->render('bindbank', ['banklist' => $arr]);
    }

    /**
     * 设置交易密码表单页.
     */
    public function actionAddbuspass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_Y;
        $data = BankService::check($this->user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('addbuspass', $data);
            }
        }

        $model = new EditpassForm();
        $model->scenario = 'add';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->editpass()) {
                return ['tourl' => '/user/user', 'code' => 1, 'message' => '添加交易密码成功'];
            }
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('addbuspass');
    }

    /**
     * 修改交易密码表单页.
     */
    public function actionEditbuspass()
    {
        $model = new EditpassForm();

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('editbuspass', ['model' => $model, 'data' => $data]);
            }
        }

        $model->scenario = 'edit';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->editpass()) {
                return ['tourl' => '/user/user', 'code' => 1, 'message' => '交易密码修改成功'];
            }
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('editbuspass', ['model' => $model]);
    }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        \Yii::$app->session->remove('cfca_qpay_recharge');
        $user = $this->user;
        $uid = $user->id;
        $user_bank = UserBanks::find()->where(['uid' => $uid])->select('id,binding_sn,bank_id,bank_name,card_number,status')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();

        //检查用户是否完成快捷支付
        $data = BankService::checkKuaijie($user);
        if ($data[code] == 1 && \Yii::$app->request->isAjax) {
            return ['next' => $data['tourl']];
        }

        return $this->render('recharge', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $uid = $user->id;

        $user_acount = $user->lendAccount;
        $user_bank = $user->bank;

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N | BankService::EDITBANK_VALIDATE;
        $data = BankService::check($user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
            }
        }

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            return ['tourl' => '/user/userbank/checktradepwd?money='.$draw->money, 'code' => 0, 'message' => ''];
        }

        if ($draw->getErrors()) {
            $message = $draw->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount]);
    }

    /**
     * 提现申请-验证交易密码表单页.
     */
    public function actionChecktradepwd($money)
    {
        $user = $this->user;
        $uid = $user->id;

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N | BankService::EDITBANK_VALIDATE;
        $data = BankService::check($user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('checktradepwd', ['status' => 0, 'data' => $data]);
            }
        }

        $user_acount = $this->user->lendAccount;
        $model = new EditpassForm();
        $model->scenario = 'checktradepwd';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $money_r = Yii::$app->request->post('money');
            if ($money != $money_r) {
                return $this->redirect('/user/userbank/tixian');
            }

            $transaction = Yii::$app->db->beginTransaction();

            $mess = [
                $user->real_name,
                date('Y-m-d H:i:s', time()),
                $money,
                Yii::$app->params['contact_tel']
            ];
            $sms = new SmsMessage([
                'uid' => $uid,
                'mobile' => $user->mobile,
                'message' => json_encode($mess),
                'level' => SmsMessage::LEVEL_LOW
            ]);

            $draw = DrawRecord::initForAccount($this->user, $money);//生成draw_record对象
            if (null === $draw) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现失败'];
            }
            if (!$draw->validate()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => current($draw->getErrors())];
            }
            if (!$draw->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现申请失败'];
            }
            //录入money_record记录
            $bc = new BcRound();
            bcscale(14);

            //提现记录
            $user_acount->available_balance = $bc->bcround(bcsub($user_acount->available_balance, $draw->money), 2);
            $money_record = new MoneyRecord();
            $money_record->sn = MoneyRecord::createSN();
            $money_record->type = MoneyRecord::TYPE_DRAW;
            $money_record->osn = $draw->sn;
            $money_record->account_id = $user_acount->id;
            $money_record->uid = $uid;
            $money_record->balance = $user_acount->available_balance;
            $money_record->out_money = $draw->money;

            //手续费记录
            $user_acount->available_balance = $bc->bcround(bcsub($user_acount->available_balance, \Yii::$app->params['drawFee']), 2);
            $mrecord = clone $money_record;
            $mrecord->sn = MoneyRecord::createSN();
            $mrecord->type = MoneyRecord::TYPE_DRAW_FEE;
            $mrecord->balance = $user_acount->available_balance;
            $mrecord->out_money = \Yii::$app->params['drawFee'];
            if (!$money_record->save() || !$mrecord->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现申请失败'];
            }

            //录入user_acount记录
            $user_acount->uid = $user_acount->uid;
            $draw->money = bcadd($draw->money, \Yii::$app->params['drawFee']);
            $user_acount->freeze_balance = $bc->bcround(bcadd($user_acount->freeze_balance, $draw->money), 2);
            $user_acount->out_sum = $bc->bcround(bcadd($user_acount->out_sum, $draw->money), 2);
            $user_acount->drawable_balance = $bc->bcround(bcsub($user_acount->drawable_balance, $draw->money), 2);
            if (!$user_acount->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现申请失败'];
            }

            $sms->template_id = Yii::$app->params['sms']['tixian_succ'];
            $sms->save();
            $transaction->commit();

            return ['tourl' => '/user/user', 'code' => 1, 'message' => '提现申请成功'];
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }
        return $this->render('checktradepwd', ['money' => $money]);
    }

    /**
     * 修改银行卡信息表单页.
     */
    public function actionEditbank()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        $model = $this->user->bank;
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                $province = array();

                return $this->render('editbank', ['model' => $model, 'province' => $province, 'data' => $data]);
            }
        }

        $model->scenario = 'step_second';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return ['tourl' => '/user/userbank/tixian', 'code' => 0, 'message' => '银行信息完善成功'];
            }
        }

        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        $province = Region::find()->where(['province_id' => 0])->select('id,name')->asArray()->all();

        return $this->render('editbank', ['model' => $model, 'province' => $province, 'data' => $data]);
    }

    /**
     * 检查银行卡号，返回开户行名称.
     */
    public function actionCheckbank()
    {
        return BankService::checkBankcard(Yii::$app->request->post('card'));
    }

    /**
     * 银行限额显示.
     */
    public function actionBankxiane()
    {
        return $this->render('bankxiane');
    }

    /**
     * 快捷支付开通说明页.
     */
    public function actionKuaijie()
    {
        return $this->render('kuaijie');
    }
}
