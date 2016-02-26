<?php

namespace console\controllers;

use Yii;
use common\models\user\RechargeRecord;
use common\service\AccountService;
use yii\console\Controller;

class RechargeController extends Controller
{
    public function actionCheck()
    {
        // TODO 先查联动的记录
        $records = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_NO])->all();
        $acc_ser = new AccountService();
        foreach ($records as $rc) {
            $resp = Yii::$container->get('ump')->getRechargeInfo($rc->sn, $rc->created_at);
            if ($resp->isSuccessful()) {
                if ("2" === $resp->get('tran_state')) {
                    $is_success = $acc_ser->confirmRecharge($rc);
                } else if ("3" === $resp->get('tran_state') || "5" === $resp->get('tran_state')) {
                    $rc->status = RechargeRecord::STATUS_FAULT;
                    $rc->save(false);
                }                
            }
        }
    }
}
