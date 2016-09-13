<?php

namespace console\controllers;

use common\models\user\RechargeRecord;
use common\service\AccountService;
use Yii;
use yii\console\Controller;

class RechargeController extends Controller
{
    public function actionCheck()
    {
        // TODO 先查联动的记录
        $records = RechargeRecord::find()
            ->where(['lastCronCheckTime' => null])
            ->orWhere(['<', 'lastCronCheckTime', time() - 5 * 60])   //查询间隔为五分钟
            ->andWhere(['status' => RechargeRecord::STATUS_NO])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $acc_ser = new AccountService();

        foreach ($records as $rc) {
            $resp = Yii::$container->get('ump')->getRechargeInfo($rc->sn, $rc->created_at);
            if ($resp->isSuccessful()) {
                if ('2' === $resp->get('tran_state')) {
                    $acc_ser->confirmRecharge($rc);
                } elseif ('3' === $resp->get('tran_state') || '5' === $resp->get('tran_state')) {
                    $rc->status = RechargeRecord::STATUS_FAULT;
                }
            }

            $rc->lastCronCheckTime = time();
            $rc->save(false);
        }
    }
}
