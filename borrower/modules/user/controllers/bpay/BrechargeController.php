<?php

namespace app\modules\user\controllers\bpay;

use Yii;
use yii\web\Controller;
use common\models\user\RechargeRecord;
use common\models\TradeLog;

class BrechargeController extends Controller
{

    /**
     * 融资用户充值前台通知接口
     */
    public function actionFrontendNotify()
    {
        $data = Yii::$app->request->get();

        TradeLog::initLog(2, $data, $data['sign']);
        if (empty($data)) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $ump = Yii::$container->get('ump');

        if ($ump->verifySign($data) && '0000' === $data['ret_code']) {
            $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

            if (!$recharge) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            $accService = Yii::$container->get('account_service');

            if ($accService->confirmRecharge($recharge)) {
                return $this->redirect('/user/useraccount/accountcenter');
            }
        }

        return $this->redirect('/user/recharge/recharge-err');
    }

    /**
     * 融资用户充值后台通知接口
     */
    public function actionBackendNotify()
    {
        $data = Yii::$app->request->get();
        $ump = Yii::$container->get('ump');
        $err = '0000';

        TradeLog::initLog(2, $data, $data['sign']);
        if ($ump->verifySign($data)) {
            $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

            if (!recharge) {
                $err = '00009999';
            } else {
                $accService = Yii::$container->get('account_service');

                if (!$accService->confirmRecharge($recharge)) {
                    $err = '00009999';
                }
            }
        } else {
            $err = '00009999';
        }

        $content = $ump->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        $this->layout = false;
        return $this->render('/recharge/recharge_notify', ['content' => $content]);
    }

}
