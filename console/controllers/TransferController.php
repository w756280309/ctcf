<?php

namespace console\controllers;

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
    public function actionQueue() {
        $transfers = Transfer::find()
            ->where(['status' => Transfer::STATUS_INIT])
            ->andWhere(['>', 'amount', 0])
            ->limit(10)
            ->orderBy(['id' => 'SORT_ASC'])
            ->all();
        if (!$transfers) {
            usleep(3000000);
            return self::EXIT_CODE_NORMAL;
        }
        $connection = Yii::$app->db;
        foreach ($transfers as $transfer) {
            $user = null;
            $user = User::findOne($transfer->user_id);
            $amount = $transfer->amount;
            if (null !== $user) {
                $transaction = $connection->beginTransaction();
                try {
                    //给用户发指定金额
                    if (AccountService::userTransfer($user, $amount)) {
                        $transfer->status = $transfer::STATUS_SUCCESS;
                    } else {
                        $transfer->status = $transfer::STATUS_FAIL;
                    }
                    $transfer->updateTime = date('Y-m-d H:i:s');
                    $transfer->save(false);
                    usleep(100000);
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        usleep(500000);
    }
}
