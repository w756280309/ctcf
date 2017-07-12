<?php

namespace api\modules\v1\controllers\tx;

use api\modules\tx\controllers\Controller;
use common\models\tx\FinUtils;
use common\models\tx\CreditNote;
use common\models\tx\UserAsset;

class CreditNoteController extends Controller
{
    /**
     * 关于债权页面js计算后台处理
     * GET请求，金额以元为单位
     *
     * @param int   $asset_id 资产ID
     * @param int   $note_id  债权ID
     * @param int   $amount   金额
     * @param float $rate     折让率
     *                        发起债权页面 请求参数:asset_id amount rate;需要返回结果['interest' => '应付利息', 'realAmount' => '折让后价格', 'fee' => '手续费'];
     *                        债权详情页 请求参数:note_id amount rate;需要返回结果['interest' => '应付利息', 'payment' => '实付金额', 'profit' => '预期收益'];
     *
     * 【金额以元为单位返回】
     */
    public function actionCalc()
    {
        $request = $this->request;
        $assetId = $request->query->getInt('asset_id');
        $noteId = $request->query->getInt('note_id');
        $amount = floatval($request->query->get('amount'));//以元为单位

        $amount = bcmul($amount, 100, 0);//以分为单位
        $rate = floatval($request->query->get('rate'));//2.5% 直接传 2.5
        $rate = max($rate, 0);

        //发起债权页面
        if ($assetId && $amount && $amount > 0 && $rate >= 0) {
            $asset = UserAsset::findOne($assetId);
            if (null === $asset || $asset->maxTradableAmount <= 0) {
                die('');
            }
            $order = $asset->order;
            $interest = FinUtils::calculateCurrentProfit($asset->loan, $amount, $order->apr);//应付利息
            $realAmount = bcmul(bcadd($amount, $interest, 14), bcsub(1, bcdiv($rate, 100, 14), 14), 0);//折让后价格
            $feeRate = \Yii::$app->params['credit']['fee_rate'];//手续费率
            $fee = bcmul($amount, $feeRate, 0);
            die('callback('.json_encode(['fee' => bcdiv($fee, 100, 2), 'interest' => bcdiv($interest, 100, 2), 'realAmount' => bcdiv($realAmount, 100, 2)]).')');
        }

        //债权购买详情页面 债权确认页面
        if ($noteId && $amount > 0) {
            $note = CreditNote::findOne($noteId);
            if (null === $note) {
                die('');
            }

            $order = $note->order;
            $interest = FinUtils::calculateCurrentProfit($note->loan, $amount, $order->apr);

            $date = date('Y-m-d');
            $plans = $note->loan->getRepaymentPlan($amount, $order->apr);
            $totalInterest = 0;

            foreach ($plans as $plan) {
                if ($plan['date'] > $date) {
                    $totalInterest = bcadd($totalInterest, $plan['interest'], 2);
                }
            }

            $profit = bcsub(bcmul($totalInterest, 100, 0), $interest, 0);
            $payment = bcmul(bcadd($interest, $amount, 14), bcsub(1, bcdiv($rate, 100, 14), 14), 0);

            die('callback('.json_encode([
                'interest' => bcdiv($interest, 100, 2),     //应付利息
                'profit' => bcdiv($profit, 100, 2),         //预期收益
                'payment' => bcdiv($payment, 100, 2),       //实付金额
                'totalInterest' => $totalInterest,          //订单对应项目总收益
            ]).')');
        }

        die('');
    }
}
