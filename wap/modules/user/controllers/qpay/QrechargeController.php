<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\lib\cfca\Cfca;
use PayGate\Cfca\Message\Request1375;
use PayGate\Cfca\Message\Request1376;
use common\models\user\RechargeRecord;
use PayGate\Cfca\Response\Response1376;
use common\models\user\MoneyRecord;
use common\lib\bchelp\BcRound;
use common\models\TradeLog;
use Yii;
use yii\base\Model;
use yii\web\Response;

class QrechargeController extends BaseController
{
    public function actionInit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cpuser = $this->user;
        $ubank = $this->ubank;
        if ($this->isDenyVisit) {
            return $this->createErrorResponse('用户被禁止访问');
        }

        if (empty($ubank)) {
            return $this->createErrorResponse('请先绑卡');
        }
        // 已验证的数据:无需验证
        $safe = [
            'uid' => $this->uid,
            'account_id' => $cpuser->lendAccount->id,
            'bindingSn' => $ubank->binding_sn,
            'bank_id' => strval($ubank->id),
            'pay_type' => RechargeRecord::PAY_TYPE_QUICK,
        ];

        $rec_model = new RechargeRecord([
            'uid' => $safe['uid'],
            'account_id' => $safe['account_id'],
            'bank_id' => $safe['bank_id'],
            'pay_bank_id' => $safe['bank_id'],
            'pay_type' => $safe['pay_type'],
        ]);
        if (
            $rec_model->load(Yii::$app->request->post())
            && $rec_model->validate()
        ) {
            $req = new Request1375(
                Yii::$app->params['cfca']['institutionId'],
                $safe['bindingSn'],
                $rec_model->fund
            );
            $cfca = new Cfca();
            $resp = $cfca->request($req);

            //记录日志
            $log = new TradeLog($cpuser, $req, $resp);
            $log->save();

            if (false === $resp) {
                return $this->createErrorResponse('服务器异常');
            } elseif ($resp instanceof CfcaResponse && !$resp->isSuccess()) {
                return $this->createErrorResponse($resp->getMessage());
            } else {
                // 设置session。用来验证数据的不可修改
                Yii::$app->session->set('cfca_qpay_recharge', [
                        'recharge_sn' => $req->getRechargeSn(),
                        'recharge_fund' => $rec_model->fund,
                        '_time' => time(),
                    ]);
                $rec_model->status = RechargeRecord::STATUS_NO;
                $rec_model->sn = $req->getRechargeSn();
                $rec_model->save();

                return ['rechargeSn' => $req->getRechargeSn()];
            }
        }

        return $this->createErrorResponse($rec_model);
    }

    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ubank = $this->ubank;
        if ($this->isDenyVisit) {
            return $this->createErrorResponse('用户被禁止访问');
        }
        if (empty($ubank)) {
            return $this->createErrorResponse('请先绑卡');
        }
        $pending = Yii::$app->session->get('cfca_qpay_recharge');
        if ($pending === null) {
            return $this->createErrorResponse('请先发送短信码');
        }
        $rc_post = Yii::$app->request->post('RechargeRecord');
        $sms = \Yii::$app->request->post('yzm');
        $from = \Yii::$app->request->post('from');
        $recharge = RechargeRecord::find()->where(['sn' => $pending['recharge_sn']])->one();

        if (empty($recharge) || $recharge->status != 0) {
            return $this->createErrorResponse('支付异常');
        }
        if (
                bccomp($rc_post['fund'], $pending['recharge_fund']) != 0
        ) {
            return $this->createErrorResponse('支付金额已经修改，请重新请求短信验证码');
        }
        $ret = $this->rechargecheckpay($recharge, $sms);
        if ($ret === true) {
            \Yii::$app->session->remove('cfca_qpay_recharge');

            return [
                'next' => empty($from) ? '/user/user' : $from,
            ];
        } else {
            return $this->createErrorResponse($ret['message']);
        }
    }

    /**
     * $recharge 充值数据
     * $yzm 中金短信
     * 快捷支付输入验证码验证支付短信码
     */
    public function rechargecheckpay($recharge, $yzm)
    {
        $rq1376 = new Request1376(
                Yii::$app->params['cfca']['institutionId'],
                $recharge->sn,
                $yzm
        );

        $cfca = new Cfca();
        $resp = $cfca->request($rq1376);
        if (false === $resp) {
            return $this->createErrorResponse('服务器异常');
        }
        $resp1376 = new Response1376($resp->getText());

        //记录日志
        $log = new TradeLog($this->user, $rq1376, $resp);
        $log->save();

        if ($resp1376->isSuccess()) {
            //测试短信末尾奇数是失败的，金额是30失败
            $bankTxTime = $resp1376->getBankTxTime();
            $user_acount = $this->user->lendAccount;
            //录入money_record记录
            $transaction = Yii::$app->db->beginTransaction();
            RechargeRecord::updateAll(['status' => RechargeRecord::STATUS_YES, 'bankNotificationTime' => $bankTxTime], ['id' => $recharge->id]);
            $bc = new BcRound();
            bcscale(14);
            $money_record = new MoneyRecord([
                'sn' => MoneyRecord::createSN(),
                'type' => MoneyRecord::TYPE_RECHARGE,
                'osn' => $recharge->sn,
                'account_id' => $this->user->lendAccount->id,
                'uid' => $this->uid,
                'balance' => $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2),
                'in_money' => $recharge->fund,
            ]);
            if (!$money_record->save()) {
                $transaction->rollBack();

                return $this->createErrorResponse('充值失败');
            }

            //录入user_acount记录
            $user_acount->uid = $user_acount->uid;
            $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $recharge->fund), 2);
            $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
            $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $recharge->fund), 2);

            if (!$user_acount->save()) {
                $transaction->rollBack();

                return $this->createErrorResponse('充值失败');
            }
            $transaction->commit();

            return true;
        } else {
            return $this->createErrorResponse('支付失败');
        }
    }

    private function createErrorResponse($modelOrMessage = null)
    {
        Yii::$app->response->statusCode = 400;
        $message = null;

        if (is_string($modelOrMessage)) {
            $message = $modelOrMessage;
        } elseif (
            $modelOrMessage instanceof Model
            && $modelOrMessage->hasErrors()
        ) {
            $message = current($modelOrMessage->getFirstErrors());
        }

        return [
            'message' => $message,
        ];
    }
}
