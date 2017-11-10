<?php

namespace common\action\loan;

use common\models\order\OnlineOrder;
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
            $expectProfit = OnlineOrder::revenue($product, $amount);
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