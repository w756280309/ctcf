<?php

namespace app\modules\user\controllers\bpay;

use Yii;
use yii\web\Controller;
use common\models\user\RechargeRecord;
use common\models\user\User;

class BrechargeController extends Controller
{

    /**
     * 融资用户充值前台通知接口
     */
    public function actionFrontendNotify()
    {
        $data = Yii::$app->request->get();

        if (empty($data)) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $ump = Yii::$container->get('ump');

        if ($ump->verifySign($data) && '0000' === $data['ret_code']) {
            $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

            if (!$recharge) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            $user = User::findOne($recharge->uid);
            if (!$user) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            $accService = Yii::$container->get('account_service');

            if ($accService->confirmRecharge($recharge, $user)) {
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

        if (empty($data)) {
            $err = '00009999';
        } else {
            if ($ump->verifySign($data) && '0000' === $data['ret_code']) {
                $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

                if (!recharge) {
                    $err = '00009999';
                } else {
                    $user = User::findOne($recharge->uid);
                    $accService = Yii::$container->get('account_service');

                    if (!$accService->confirmRecharge($recharge, $user)) {
                        $err = '00009999';
                    }
                }
            } else {
                $err = '00009999';
            }
        }

        $content = $ump->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('recharge_notify', ['content' => $content]);
    }

}
