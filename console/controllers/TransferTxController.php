<?php

namespace console\controllers;

use common\models\transfer\TransferTx;
use common\service\AccountService;
use Yii;
use yii\console\Controller;

class TransferTxController extends Controller
{
    /**
     * 授权转账订单console，更新订单状态并进行相应处理
     *
     * - 查询一天内的订单
     * - 查询状态 0初始 1处理中
     * - 每次查询5条
     * - 按上次查询时间升序
     */
    public function actionCheck()
    {
        $transferTxs = TransferTx::find()
            ->where(['<', 'status', TransferTx::STATUS_SUCCESS])
            ->andWhere(['>', 'createTime', date('Y-m-d H:i:s', strtotime('-1 day'))])
            ->orderBy(['lastCronCheckTime' => SORT_ASC])
            ->limit(5)
            ->all();
        $ump = Yii::$container->get('ump');
        $accountService = new AccountService();
        foreach ($transferTxs as $transferTx) {
            //先将上次查询时间更新
            $transferTx->lastCronCheckTime = date('Y-m-d H:i:s');
            $transferTx->save(false);

            //主动查询订单是否成功
            $transferInfo = $ump->getOrderInfo1($transferTx->sn, date('Ymd', strtotime($transferTx->createTime)));

            //如果成功，更新订单状态及扣减温都余额及添加流水记录，失败更新状态
            if ($transferInfo->isSuccessful()) {
                $accountService->confirmTransfer($transferTx);
            } elseif ('00240000' === $transferInfo->get('ret_code')) {
                //需要特殊处理
                $transferTx->status = TransferTx::STATUS_UNKNOWN;
                $transferTx->save(false);
            } else {
                Yii::info('转账失败处理：【'.$transferInfo->get('ret_code').'】'.$transferInfo->get('ret_msg'), 'user_log');
                $transferTx->status = TransferTx::STATUS_FAILURE;
                $transferTx->save(false);
            }
        }
    }
}
