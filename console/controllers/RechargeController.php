<?php

namespace console\controllers;

use common\models\queue\QueueTask;
use common\models\user\RechargeRecord;
use common\models\user\User;
use common\service\AccountService;
use Yii;
use yii\console\Controller;

class RechargeController extends Controller
{
    public function actionCheck()
    {
        $records = RechargeRecord::find()
            ->where(['status' => RechargeRecord::STATUS_NO])
            ->andWhere(['>', 'created_at', strtotime('-3 day')])
            ->orderBy(['lastCronCheckTime' => SORT_ASC])
            ->limit(3)
            ->all();

        $acc_ser = new AccountService();

        foreach ($records as $rc) {
            $rc->lastCronCheckTime = time();
            $rc->save(false);

            $resp = Yii::$container->get('ump')->getRechargeInfo($rc->sn, $rc->created_at);
            if ($resp->isSuccessful()) {
                if ('2' === $resp->get('tran_state')) {
                    $acc_ser->confirmRecharge($rc);
                } elseif ('3' === $resp->get('tran_state')) {
                    $rc->status = RechargeRecord::STATUS_FAULT;
                }
            }

            $rc->save(false);
        }
    }
}
