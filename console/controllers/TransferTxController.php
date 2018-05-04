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
        exit;
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
            $transferInfo = $ump->getTransferInfo($transferTx->sn, date('Ymd', strtotime($transferTx->createTime)));

            //如果成功，更新订单状态及扣减温都余额及添加流水记录，失败更新状态
            if ($transferInfo->isSuccessful()) {
                $tranState = $transferInfo ->get('tran_state');
                if ('2' === $tranState) {
                    $accountService->confirmTransfer($transferTx);
                } elseif ('3' === $tranState) {
                    $transferTx->status = TransferTx::STATUS_FAILURE;
                    $transferTx->save(false);
                }
            }
        }
    }
}
