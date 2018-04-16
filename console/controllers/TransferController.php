<?php

namespace console\controllers;

use common\models\epay\EpayUser;
use common\models\transfer\Transfer;
use common\models\user\User;
use common\service\AccountService;
use Yii;
use yii\console\Controller;

/**
 * 平台给虚拟账户发送红包工作队列
 */
class TransferController extends Controller
{
    /**
     * 平台给虚拟账户发送红包工作队列
     * 循环调用，每次取10条记录，处理成功后sleep（500000）
     * 如果没有符合的记录，则usleep（3000000）
     * 方法名称需要确认
     */
    public function actionQueue()
    {
        $transferTable = Transfer::tableName();
        $epayUserTable = EpayUser::tableName();

        $transfers = Transfer::find()
            ->where(["$transferTable.status" => Transfer::STATUS_INIT])
            ->andWhere(['>', "$transferTable.amount", 0])
            ->orderBy([
                "$transferTable.lastCronCheckTime" => SORT_ASC,
                "$transferTable.id" => SORT_ASC,
            ])->limit(10)
            ->all();
        if (!$transfers) {
            usleep(3000000);
            return self::EXIT_CODE_NORMAL;
        }
        /**
         * @var Transfer $transfer
         */
        foreach ($transfers as $transfer) {
            $epayUser = EpayUser::findOne(['appUserId' => (string)$transfer->user_id]);
            if (null === $epayUser) {
                continue;
            }
            //无论发放成功失败与否，都写入上次执行时间
            $sql = "update $transferTable set lastCronCheckTime = :time, `status` = :status where id = :transferId and user_id = :userId and `status` = '" . Transfer::STATUS_INIT . "'";
            $affectedRows = Yii::$app->db->createCommand($sql, [
                'time' => time(),
                'status' => Transfer::STATUS_PENDING,
                'transferId' => $transfer->id,
                'userId' => $transfer->user_id,
            ])->execute();
            if ($affectedRows !== 1) {
                continue;
            }
            $user = User::findOne($transfer->user_id);
            if (is_null($user)) {
                continue;
            }
            $message = '';
            try {
                //给用户发指定金额
                if (AccountService::userTransfer($user, $transfer->amount, $transfer->sn)) {
                    $transfer->status = Transfer::STATUS_SUCCESS;
                } else {
                    $transfer->status = Transfer::STATUS_FAIL;
                }

            } catch (\Exception $e) {
                $transfer->status = Transfer::STATUS_FAIL;
                $message = $e->getMessage();
            }
            $status = ($transfer->status === Transfer::STATUS_SUCCESS) ? "成功" : "失败";
            Yii::info("[transfer_queue][user_transfer] 红包队列[{$transfer->id}] 用户[{$user->id}] 转账{$transfer->amount} 元, 状态: " . $status . ', 信息: '. $message, 'queue');
            $transfer->updateTime = date('Y-m-d H:i:s');
            $transfer->save(false);
            usleep(100000);
        }
        usleep(500000);
    }
}
