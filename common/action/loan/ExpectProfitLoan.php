<?php

namespace common\action\loan;

use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * 服务端计算购买标的的预期收益
 */
class ExpectProfitLoan extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (!Yii::$app->request->isAjax) {
                throw new \Exception('请求参数错误');
            }
            $sn = Yii::$app->request->post('sn');
            $amount = Yii::$app->request->post('amount');
            $product = OnlineProduct::find()->where(['sn' => $sn])->one();
            if (is_null($product)) {
                throw new \Exception('数据未找到');
            }
            if ($amount <= 0) {
                throw new \Exception('投资金额异常');
            }
            $realRate = null;
            if (1 === $product->isFlexRate && !empty($product->rateSteps)) {
                $config = RateSteps::parse($product->rateSteps);
                if (!empty($config)) {
                    $rate = RateSteps::getRateForAmount($config, $amount);
                    if (false !== $rate) {
                        $realRate = bcdiv($rate, 100, 6);
                    }
                }
            }
            if (is_null($realRate)) {
                $realRate = $product->yield_rate;
            }

            $expectProfit = OnlineProduct::calcExpectProfit($amount, $product->refund_method, $product->expires, $realRate);
            return [
                'code' => 0,
                'interest' => $expectProfit,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => $e->getMessage(),
            ];
        }
    }
}