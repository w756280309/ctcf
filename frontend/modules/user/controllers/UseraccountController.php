<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Response;
use frontend\controllers\BaseController;
use common\models\user\EditpassForm;
use common\service\UserService;
use common\models\user\UserAccount;
use common\models\user\MoneyRecord;
use common\service\BankService;
use common\models\user\UserBanks;
use common\models\user\DrawRecord;
use common\models\city\Region;
use common\lib\bchelp\BcRound;
use common\models\sms\SmsMessage;

class UseraccountController extends BaseController
{
    public $layout = '@app/views/layouts/main';

    /**
     * 账户中心展示页.
     */
    public function actionAccountcenter()
    {
        $uid = $this->user->id;
        $check_arr = $this->check_helper();

        if ($check_arr['code'] === 1) {
            $errflag = 1;
            $errmess = $check_arr[message];
        }

        $account = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $uid]);

        return $this->render('accountcenter', ['model' => $account, 'username' => $this->user->real_name, 'errflag' => $errflag, 'errmess' => $errmess]);
    }

    /**
     * 提现页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $uid = $user->id;
        $user_bank = UserBanks::findOne(['uid' => $uid, 'status' => UserBanks::STATUS_YES]);
        $user_acount = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $uid]);
        $province = Region::find()->where(['province_id' => 0])->select('id,name')->asArray()->all();

        if ($user_acount->out_sum == 0) {
            $check_arr = $this->check_helper();
            if ($check_arr[code] == 1) {
                return $this->goHome();
            }
        }

        $model = new EditpassForm();
        $draw = new DrawRecord();
        $model->scenario = 'checktradepwd';
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $us = new UserService();
            $re = $us->checkDraw($uid, $draw->money);
            if ($re['code']) {
                $draw->addError('money', $re['message']);
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                $mess = [
                    $user->real_name,
                    date('Y-m-d H:i:s', time()),
                    $draw->money,
                    Yii::$app->params['contact_tel']
                ];
                $sms = new SmsMessage([
                    'uid' => $uid,
                    'mobile' => $user->mobile,
                    'level' => SmsMessage::LEVEL_LOW,
                    'message' => json_encode($mess)
                ]);

                //录入draw_record记录
                $draw->sn = DrawRecord::createSN();
                $draw->pay_id = 0;
                $draw->account_id = $user_acount->id;
                $draw->pay_bank_id = '0';
                $draw->bank_id = $user_bank->bank_id;
                $draw->bank_name = $user_bank->bank_name;
                $draw->bank_account = '0';
                $draw->status = DrawRecord::STATUS_ZERO;

                if (!$draw->save()) {
                    $transaction->rollBack();
                    $sms->template_id = Yii::$app->params['sms']['tixian_err'];
                    $sms->save();
                    return $this->redirect('/user/useraccount/tixianback?flag=err');
                }

                //录入money_record记录
                $bc = new BcRound();
                bcscale(14);
                $money_record = new MoneyRecord();
                $money_record->sn = MoneyRecord::createSN();
                $money_record->type = MoneyRecord::TYPE_DRAW;
                $money_record->osn = $draw->sn;
                $money_record->account_id = $user_acount->id;
                $money_record->uid = $uid;
                $money_record->balance = $bc->bcround(bcsub($user_acount->available_balance, $draw->money), 2);
                $money_record->out_money = $draw->money;

                if (!$money_record->save()) {
                    $transaction->rollBack();
                    $sms->template_id = Yii::$app->params['sms']['tixian_err'];
                    $sms->save();
                    return $this->redirect('/user/useraccount/tixianback?flag=err');
                }

                //录入user_acount记录
                $user_acount->uid = $user_acount->uid;
                $user_acount->available_balance = $bc->bcround(bcsub($user_acount->available_balance, $draw->money), 2);
                $user_acount->freeze_balance = $bc->bcround(bcadd($user_acount->freeze_balance, $draw->money), 2);

                if (!$user_acount->save()) {
                    $transaction->rollBack();
                    $sms->template_id = Yii::$app->params['sms']['tixian_err'];
                    $sms->save();
                    return $this->redirect('/user/useraccount/tixianback?flag=err');
                }

                $sms->template_id = Yii::$app->params['sms']['tixian_succ'];
                $sms->save();

                $transaction->commit();

                return $this->redirect('/user/useraccount/tixianback?flag=succ');
            }
        }

        return $this->render('tixian', ['model' => $model, 'bank' => $user_bank, 'user_account' => $user_acount, 'draw' => $draw, 'province' => $province]);
    }

    /**
     * 补充银行信息.
     */
    public function actionEditbank()
    {
        $res = false;
        $message = '操作失败';
        $check_arr = $this->check_helper();
        if ($check_arr['code'] == 1) {
            $this->goHome();
        }

        $bank = $check_arr['user_bank'];
        $bank->scenario = 'step_second';
        if ($bank->load(Yii::$app->request->post()) && $bank->validate()) {
            $res = $bank->save();
            if ($res) {
                $message = '操作成功';
            }
        }

        if ($bank->hasErrors()) {
            $message = current($bank->firstErrors);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['res' => $res, 'message' => $message];
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

    /**
     * 检查实名认证过程是否完成.
     */
    public function check_helper()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;

        return BankService::check($this->user, $cond);
    }

    /**
     * 查询省份对应的城市
     */
    public function actionCity($pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $city = Region::find()->where(['province_id'=>$pid])->select('name')->asArray()->all();

        return ['name' => $city];
    }
}
