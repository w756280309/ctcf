<?php

namespace app\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\user\UserAccount;
use common\models\TradeLog;
use PayGate\Cfca\Message\Request1311;
use PayGate\Cfca\Message\Request1320;
use PayGate\Cfca\Response\Response1320;
use common\lib\cfca\Cfca;
use app\modules\user\controllers\bpay\BrechargeController;

class RechargeController extends BaseController {

    public $layout = false;
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    /**
     * 充值
     */
    public function actionRecharge() {
        $uid = $this->user->id;
        $bank = Yii::$app->params['bank'];

        $user_account = UserAccount::findOne(['uid' => $uid, 'type' => UserAccount::TYPE_LEND]);
        $recharge = new RechargeRecord([
            'uid' => $uid,
            'account_id' => $user_account->id
        ]);

        if ($recharge->load(Yii::$app->request->post())) {
            $bank_id = Yii::$app->request->post('bankid');
            $account_type = Yii::$app->request->post('account_type');
            $pay_type = Yii::$app->request->post('pay_type');

            if (empty($bank_id) || empty($account_type) || empty($pay_type)) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            if (!in_array($account_type, ['11', '12'])) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            $recharge->sn = RechargeRecord::createSN();
            $recharge->pay_id = 0;
            $recharge->account_id = $user_account->id;
            $recharge->bank_id = $bank_id;
            $recharge->pay_bank_id = $bank_id;
            $recharge->bankNotificationTime = '0';
            $recharge->status = RechargeRecord::STATUS_NO;
            $recharge->pay_type = $pay_type;

            if ($recharge->validate()) {
                $req = new Request1311(
                        Yii::$app->params['cfca']['institutionId'],
                        $recharge,
                        $account_type
                );

                $cfca = new Cfca();
                $resp = $cfca->request($req);

                if (null === $resp) {
                    return $this->redirect('/user/recharge/recharge-err');
                } else {
                    //录入recharge_record记录
                    if (!$recharge->save()) {
                        return $this->redirect('/user/recharge/recharge-err');
                    }

                    // 设置session。用来验证数据的不可修改
                    Yii::$app->session->set('cfca_recharge', [
                        'recharge_sn' => $req->getRechargeSn()
                    ]);

                    //录入日志信息
                    $trade_log = new TradeLog($this->user, $req, null);
                    $trade_log->save();
                }

                echo $resp;
                exit;
            }
        }

        return $this->render('recharge', ['recharge' => $recharge, 'user_account' => $user_account, 'bank' => $bank]);
    }

    /**
     * 查询充值状态 1320
     */
    public function actionCheckarchstatus() {
        $record = Yii::$app->session->get('cfca_recharge');

        if (null === $record) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $req = new Request1320(
                Yii::$app->params['cfca']['institutionId'],
                $record['recharge_sn']
        );

        $cfca = new Cfca();
        $resp = $cfca->request($req);
        $rp1320 = new Response1320($resp->getText());

        //录入日志信息
        $trade_log = new TradeLog($this->user, $req, $resp);
        $trade_log->save();

        if ($resp->isSuccess()) {
            if ($rp1320->isSuccess()) {
                $recharge = RechargeRecord::findOne(['sn' => $req->getPaymentNo()]);
                $recharge->bankNotificationTime = $rp1320->getBanknotificationtime();

                if (empty($recharge)) {
                    return $this->redirect('/user/recharge/recharge-err');
                }

                if (BrechargeController::is_updateAccount($recharge, $this->user)) {
                    \Yii::$app->session->remove('cfca_recharge');
                    return $this->redirect('/user/useraccount/accountcenter');
                } else {
                    return $this->redirect('/user/recharge/recharge-err');
                }
            } else {
                return $this->redirect('/user/recharge/recharge-err');
            }
        } else {
            return $this->redirect('/user/recharge/recharge-err');
        }
    }

    /**
     * 充值失败页面
     */
    public function actionRechargeErr() {
        return $this->render('recharge_err');
    }

}
