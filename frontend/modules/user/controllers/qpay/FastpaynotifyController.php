<?php
/**
 * Created by PhpStorm.
 * User: cz
 * Date: 2018/11/7
 * Time: 16:54
 */
namespace frontend\modules\user\controllers\qpay;

use frontend\controllers\BaseController;
use Yii;
use common\models\TradeLog;
use common\models\epay\EpayUser;
use common\models\user\User;
use common\service\BankService;
use common\models\user\UserFreepwdRecord;
class FastpaynotifyController extends BaseController
{
    /**
     * 快捷支付
     * 前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data, 'pay');
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N  | BankService::BINDBANK_VALIDATE_N;
            $ret = BankService::check($user, $cond, true);
            if (1 === (int)$ret['code']) {
                //跳到来源页面，如从充值过来的跳到充值页面；
                if (\Yii::$app->session->has('to_url')) {
                    $url = \Yii::$app->session->get('to_url');
                    \Yii::$app->session->remove('to_url');
                    return $this->redirect($url);
                }
                return $this->goReferrer($ret['tourl']);
            }else{
                if('0000' === $data['ret_code']){//跳转
                    return $this->redirect('/user/userbank/recharge-depute');
                }else{
                    throw new \Exception($data['order_id'] . '快捷支付开通失败');
                }
            }
        } catch (\Exception $ex) {
        }
    }

    /**
     * 快捷支付
     * 后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data, 'pay');
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N  | BankService::BINDBANK_VALIDATE_N;
            $ret = BankService::check($user, $cond, true);
            if (1 === (int)$ret['code']) {
                $errmsg = $data['message'];
            } else {
                $err = '0000';
            }
        } catch (\Exception $ex) {
            $errmsg = $ex->getMessage();
        }
        $content = Yii::$container->get('ump')->buildQuery([
            'reg_code' => $err,
        ]);
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    /**
     * 免密充值
     * 前台通知地址
     */
    public function actionRefrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data, 'repay');
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N  | BankService::BINDBANK_VALIDATE_N;
            $ret = BankService::check($user, $cond, true);
            if (1 === (int)$ret['code']) {
                //跳到来源页面，如从充值过来的跳到充值页面；
                if (\Yii::$app->session->has('to_url')) {
                    $url = \Yii::$app->session->get('to_url');
                    \Yii::$app->session->remove('to_url');
                    return $this->redirect($url);
                }
                return $this->goReferrer($ret['tourl']);
            }
        } catch (\Exception $ex) {
        }
        return $this->redirect('/user/userbank/recharge-depute');
    }

    /**
     * 免密充值
     * 后台通知地址
     */
    public function actionRebackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data, 'repay');
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N  | BankService::BINDBANK_VALIDATE_N;
            $ret = BankService::check($user, $cond, true);
            if (1 === (int)$ret['code']) {
                $errmsg = $data['message'];
            } else {
                $err = '0000';
            }
        } catch (\Exception $ex) {
            $errmsg = $ex->getMessage();
        }
        $content = Yii::$container->get('ump')->buildQuery([
            'reg_code' => $err,
        ]);
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    public function processing($data, $step='')
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'mer_bind_agreement_notify' === $data['service']
        ) {
            if('pay' == $step){
                $status = UserFreepwdRecord::OPEN_FASTPAY_STATUS_PASS;
            }else if('repay' == $step){
                $status = UserFreepwdRecord::OPEN_FREE_RECHARGE_PASS;
            }

        } else {
            if('pay' == $step){
                $status = UserFreepwdRecord::OPEN_FASTPAY_STATUS_UNPASS;
            }else if('repay' == $step){
                $status = UserFreepwdRecord::OPEN_FREE_RECHARGE_UNPASS;
            }
        }
        $epayUser = EpayUser::findOne(['epayUserId' => $data['user_id']]);
        $upt = UserFreepwdRecord::updateAll(["status" => $status, 'ret_code'=> $data['ret_code'], 'ret_msg'=> $data['ret_msg']],  ['uid'=> $epayUser->appUserId, 'epayUserId' => $data['user_id']]);
        if($upt){
            return User::findOne($epayUser->appUserId);
        }else{
            throw new \Exception($data['order_id'] . '处理失败');
        }
    }
}