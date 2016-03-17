<?php

namespace api\modules\v1\controllers;

use Yii;
use common\models\TradeLog;
use common\models\user\RechargeRecord;
use common\models\epay\EpayUser;
use common\service\AccountService;
use common\models\user\UserAccount;
use yii\web\Response;

/**
 * POS充值API.
 */
class PosController extends Controller
{
    public function actionNotify()
    {
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_HTML;
        $err = '00009999';
        $errmsg = 'no error';
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
                1 == 1
            //Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'recharge_notify' === $data['service']
        ) {
            if (
                array_key_exists('amount', $data) && null !== $data['amount']
                && array_key_exists('user_id', $data) && null !== $data['user_id']
                && array_key_exists('account_id', $data) && null !== $data['account_id']
                && array_key_exists('mobile_id', $data) && null !== $data['mobile_id']
            ) {
                $epayUser = EpayUser::findOne(['epayUserId' => $data['user_id']]);
                if (null === $epayUser) {
                    $errmsg = $data['user_id'].'此用户不存在';
                }
                $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);
                if ($recharge) {
                    $errmsg = $data['order_id'].'线下充值已成功';
                } else {
                    $ua = UserAccount::findOne(['uid' => $epayUser->appUserId]);
                    $rc = new RechargeRecord([
                    'sn' => $data['order_id'],
                    'uid' => $epayUser->appUserId,
                    'fund' => $data['amount'] / 100,
                    'account_id' => $ua->id, ///待定
                    'bank_id' => '0',
                    'pay_bank_id' => '0',
                    'pay_type' => RechargeRecord::PAY_TYPE_POS,
                    'clientIp' => ip2long(Yii::$app->request->userIP),
                    'epayUserId' => $epayUser->epayUserId,
                    'status' => 0,
                ]);
                    if (!$rc->validate()) {
                        $errmsg = $data['order_id'].'充值失败:'.$rc->getSingleError()['message'];
                    }
                    $rc->save(false);
                    $acc_ser = new AccountService();
                    $is_success = $acc_ser->confirmRecharge($rc);
                    if ($is_success) {
                        $err = '0000';
                        $errmsg = '操作成功';
                    } else {
                        $errmsg = $data['order_id'].'充值失败';
                    }
                }
            }
        } else {
            $errmsg = $data['order_id'].'充值失败,状态:'.$data['ret_code'];
        }

        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}
