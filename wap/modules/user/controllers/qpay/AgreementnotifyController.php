<?php

namespace app\modules\user\controllers\qpay;

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
     * http://1.202.51.191:8001/user/qpay/agreementnotify/frontend?mer_id=7050209&ret_code=0000&ret_msg=%E6%88%90%E5%8A%9F&service=mer_bind_agreement_notify&sign_type=RSA&user_bind_agreement_list=ZTBB0G00%2C0000%2C%E6%88%90%E5%8A%9F&user_id=UB201603221116410000000000049168&version=1.0&sign=LkdqyitjSTUzq8IucALZO2%2BVGadykY3YTIqTtNiouMp%2FmmHjOlyLBQRTZB32tQ9u18jKuajsA8Itlk7R5woYX%2FwlM64MmXTjYW1oZalDl6GvteBj85%2B1uPzRjtqmy5ZirDn5yiw8Cq5TRpEA2cK%2BNgSY1agPdVGJnIonCE2B%2BiI%3D
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        try {
            $user = $this->processing($data);
            $ret = BankService::checkKuaijie($user);
            if (1 === (int)$ret['code']) {
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
        //TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'mer_bind_agreement_notify' === $data['service']
        ) {
            $epayUser = EpayUser::findOne(['epayUserId' => $data['user_id']]);
            User::updateAll(["mianmiStatus" => 1], "id=" . $epayUser->appUserId);
            return User::findOne($epayUser->appUserId);
        } else {
            throw new \Exception($data['order_id'].'处理失败');
        }
        
    }
}
