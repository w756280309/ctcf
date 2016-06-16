<?php

namespace frontend\modules\user\controllers\qpay;

use Yii;
use yii\web\Controller;
use common\models\TradeLog;
use common\models\epay\EpayUser;
use common\models\user\User;
use common\service\BankService;

/**
 * 免密支付回调地址
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class AgreementnotifyController extends Controller
{
    /**
     * 前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data);
            $ret = BankService::checkKuaijie($user);
            if (1 === (int)$ret['code']) {
                //跳到来源页面，如从充值过来的跳到充值页面；
                return $this->redirect($ret['tourl']);
            }
        } catch (\Exception $ex) {
        }
        return $this->redirect('/user/user');
    }

    /**
     * 后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data);
            $ret = BankService::checkKuaijie($user);
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

    public static function processing($data)
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
            $epayUser = EpayUser::findOne(['epayUserId' => $data['user_id']]);
            User::updateAll(["mianmiStatus" => 1], "id=" . $epayUser->appUserId);
            return User::findOne($epayUser->appUserId);
        } else {
            throw new \Exception($data['order_id'] . '处理失败');
        }

    }
}
