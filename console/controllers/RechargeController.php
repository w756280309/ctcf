<?php

namespace console\controllers;

use common\models\user\RechargeRecord;
use common\service\AccountService;
use yii\console\Controller;

class RechargeController extends Controller
{
    public function actionCheck($sn)
    {
        // TODO 先查联动的记录
        $rc = RechargeRecord::findOne(['sn' => $sn]);
        if (null !== $rc) {
            $acc_ser = new AccountService();
            $is_success = $acc_ser->confirmRecharge($rc);
        }
    }
}
