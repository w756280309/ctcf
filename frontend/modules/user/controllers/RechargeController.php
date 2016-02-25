<?php

namespace app\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use PayGate\Cfca\CfcaUtils;
use common\service\BankService;

class RechargeController extends BaseController
{
    public $layout = '@app/views/layouts/main';

    public function beforeAction($action)
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;

        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $this->redirect('/user/useraccount/accountcenter');
        }

        parent::beforeAction($action);
    }

    /**
     * 充值
     */
    public function actionInit()
    {
        $bank = Yii::$app->params['bank'];

        $recharge = new RechargeRecord();
        $user_account = $this->user->lendAccount;

        return $this->render('recharge', ['recharge' => $recharge, 'user_account' => $user_account, 'bank' => $bank]);
    }

    /**
     * 充值申请.
     */
    public function actionApply()
    {
        $bank_id = Yii::$app->request->post('bankid');
        $pay_type = Yii::$app->request->post('pay_type');   //目前只支持网银充值 2

        if (empty($bank_id) || empty($pay_type)) {
            throw new \Exception('The argument bank_id or pay_type is null.');
        }

        $recharge = new RechargeRecord([
            'sn' => CfcaUtils::generateSn("RC"),
            'pay_type' => $pay_type,
            'pay_id' => 0,
            'account_id' => $this->user->lendAccount->id,
            'uid' => $this->user->id,
            'bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'pay_bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'status' => RechargeRecord::STATUS_NO,
            'clientIp' => ip2long(Yii::$app->request->userIP),
            'epayUserId' => $this->user->epayUser->epayUserId,
        ]);

        if ($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            //录入recharge_record记录
            if (!$recharge->save(false)) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            // 设置session。用来验证数据的不可修改
            Yii::$app->session->set('epayLend_brecharge', [
                'recharge_sn' => $recharge->sn,
            ]);

            $ump = Yii::$container->get('ump');

            $ump->rechargeViaBpay($recharge, Yii::$app->params['bank'][$bank_id]['nickname']);
        } else {
            return $this->redirect('/user/recharge/recharge-err');
        }
    }

    /**
     * 充值结果查询.
     */
    public function actionQuery()
    {
        $record = Yii::$app->session->get('epayLend_brecharge');

        if (empty($record['recharge_sn'])) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $recharge = RechargeRecord::findOne(['sn' => $record['recharge_sn']]);

        if (!$recharge) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $ump = Yii::$container->get('ump');

        $resp = $ump->getRechargeInfo(
            $recharge->sn, $recharge->created_at
        );

        if ($resp->isSuccessful()) {
            $accService = Yii::$container->get('account_service');

            if ('2' === $resp->get('tran_state')) {
                if ($accService->confirmRecharge($recharge)) {
                    \Yii::$app->session->remove('epayLend_brecharge');

                    return $this->redirect('/user/useraccount/accountcenter');
                } elseif ('3' === $resp->get('tran_state') || '5' === $resp->get('tran_state')) {
                    if ($accService->cancelRecharge($recharge)) {
                        \Yii::$app->session->remove('epayLend_brecharge');
                    }
                }
            }
        }

        return $this->redirect('/user/recharge/recharge-err');
    }

    /**
     * 充值失败页面.
     */
    public function actionRechargeErr()
    {
        return $this->render('recharge_err');
    }
}
