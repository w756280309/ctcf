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
        // TODO 先查联动的记录
        $records = RechargeRecord::find()
            ->where(['lastCronCheckTime' => null])
            ->orWhere(['<', 'lastCronCheckTime', time() - 5 * 60])   //查询间隔为五分钟
            ->andWhere(['status' => RechargeRecord::STATUS_NO])
            ->andWhere(['>', 'created_at', strtotime('-3 day')])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $acc_ser = new AccountService();

        foreach ($records as $rc) {
            $resp = Yii::$container->get('ump')->getRechargeInfo($rc->sn, $rc->created_at);
            if ($resp->isSuccessful()) {
                if ('2' === $resp->get('tran_state')) {
                    $acc_ser->confirmRecharge($rc);
                } elseif ('3' === $resp->get('tran_state')) {
                    $rc->status = RechargeRecord::STATUS_FAULT;
                    $user = User::findOne($rc->uid);
                    if (!is_null($user)) {
                        $command = 'queue/recharge-notify '. base64_encode(json_encode([
                                'userId' => $user->id,
                                'rechargeSn' => $rc->sn,
                                'message' => 'ret_stats:'.$resp->get('tran_state').';ret_msg:'.$resp->get('ret_msg'),
                                'dateTime' => date('Y-m-d H:i:s', $rc->created_at),
                            ]));
                        \Yii::$container->get('db_queue')->push(QueueTask::createNewTask('recharge_fail_notify', $command));
                    }
                }
            }

            $rc->lastCronCheckTime = time();
            $rc->save(false);
        }
    }
}
