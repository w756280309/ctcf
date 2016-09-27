<?php

namespace common\action\credit;

use Yii;
use yii\base\Action;

class CreateAction extends Action
{
    //ajax请求发起挂牌记录
    public function run()
    {
        $asset_id = \Yii::$app->request->post('asset_id');
        $amount = floatval(\Yii::$app->request->post('amount'));
        $rate = floatval(\Yii::$app->request->post('rate', 0));
        $rate = $rate ?: 0;
        if ($asset_id > 0 && $amount > 0 && $rate >= 0) {
            try {
                $txClient = \Yii::$container->get('txClient');
                $result = $txClient->post('credit-note/new', [
                    'discountRate' => $rate,
                    'asset_id' => $asset_id,
                    'amount' => bcmul($amount, 100, 0),
                ]);
                $responseData = ['code' => 0, 'data' => $result];
            } catch (\Exception $e) {
                $result = json_decode(strval($e->getResponse()->getBody()), true);
                if (isset($result['name'])
                    && $result['name'] === 'Bad Request'
                    && isset($result['message'])
                    && isset($result['status'])
                    && $result['status'] !== 200
                ) {
                    //获取没有指定属性的错误信息
                    $responseData = ['code' => 1, 'data' => [['msg' => $result['message'], 'attribute' => '']]];
                } else {
                    //获取有指定属性的错误信息
                    $data = [];
                    foreach ($result as $attribute => $message) {
                        $data[] = ['attribute' => $attribute, 'msg' => $message];
                    }
                    $responseData = ['code' => 1, 'data' => $data];
                }
            }
        } else {
            $responseData = ['code' => 1, 'data' => [['msg' => '参数错误', 'attribute' => '']]];
        }

        return $responseData;
    }
}
