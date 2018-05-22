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
     * - 查询3天内到当前时间3分钟前的订单
     * - 更新状态 0初始 1处理中 2成功 3失败
     * - 每次查询5条
     * - 按上次查询时间升序
     *
     * 订单查询接口返回：
     *
     * tran_state取值范围含义：
     * 0初始、2成功、3失败、4不明、5、交易关闭、6其他
     */
    public function actionCheck()
    {
        $threeMinutesAgo = time() - 3 * 60;
        $transferTxs = TransferTx::find()
            ->where(['<', 'status', TransferTx::STATUS_SUCCESS])
            ->andWhere(['>', 'createTime', date('Y-m-d H:i:s', strtotime('-3 day'))])
            ->andWhere(['<', 'createTime', date('Y-m-d H:i:s', $threeMinutesAgo)])
            ->orderBy(['lastCronCheckTime' => SORT_ASC])
            ->limit(5)
            ->all();

        $ump = Yii::$container->get('ump');
        $accountService = new AccountService();
        foreach ($transferTxs as $transferTx) {
            //先将上次查询时间更新
            $transferTx->lastCronCheckTime = date('Y-m-d H:i:s');
            $transferTx->save(false);

            //主动查询订单是否成功，失败，其他状态不再处理
            $transferInfo = $ump->getOrderInfo1($transferTx->sn, date('Ymd', strtotime($transferTx->createTime)));
            //如果成功，更新订单状态及扣减温都余额及添加流水记录，失败更新状态
            if ($transferInfo->isSuccessful()) {
                $tranState = (int) $transferInfo->get('tran_state');
                //订单成功
                if (2 === $tranState) {
                    $accountService->confirmTransfer($transferTx);
                //订单失败
                } elseif (3 === $tranState) {
                    $transferTx->status = TransferTx::STATUS_FAILURE;
                    $transferTx->save(false);
                }
            } else {
                Yii::info('查询失败订单sn：' . $transferTx->sn . '【'.$transferInfo->get('ret_code').'】'.$transferInfo->get('ret_msg'), 'user_log');
            }
        }
    }

    /**
     * 检查某个授权订单的状态
     *
     * @param string $sn 订单流水号
     * @param string $issueDate 订单日期（PHP:Ymd），形如20180510
     *
     * @return void
     */
    public function actionSingleViaSn($sn, $issueDate)
    {
        $ump = Yii::$container->get('ump');
        $res = $ump->getOrderInfo1($sn, $issueDate);
        var_dump($res);
    }
}
