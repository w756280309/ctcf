<?php

namespace frontend\modules\credit\controllers;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use frontend\controllers\BaseController;
use GuzzleHttp\Client;

class NoteController extends BaseController
{
    //发起债权页面
    public function actionNew($asset_id)
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }
        //获取资产详情
        $client = new Client(['base_uri' => rtrim(\Yii::$app->params['clientOption']['host']['tx'], '/')]);
        $res = $client->request('GET', '/Api/assets/detail?id=' . $asset_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $asset = json_decode($res->getBody(), true);
        if (!$asset) {
            throw $this->ex404('没有找到指定资产');
        }
        $loan = OnlineProduct::findOne($asset['loan_id']);
        $order = OnlineOrder::findOne($asset['order_id']);
        $apr = $order->yield_rate;

        return $this->render('new', [
            'asset' => $asset,
            'loan' => $loan,
            'apr' => $apr,
        ]);
    }

    //ajax请求发起挂牌记录
    public function actionCreate()
    {
        $asset_id = \Yii::$app->request->post('asset_id');
        $amount = \Yii::$app->request->post('amount');
        $rate = \Yii::$app->request->post('rate', 0);
        $rate = $rate ?: 0;
        if ($asset_id > 0 && $amount > 0 && $rate >= 0) {
            try {
                $client = new Client(['base_uri' => rtrim(\Yii::$app->params['clientOption']['host']['tx'], '/')]);
                $res = $client->request('POST', '/Api/credit-note/new', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'discountRate' => $rate,
                        'asset_id' => $asset_id,
                        'amount' => $amount * 100,
                    ],
                ]);
                $result = json_decode($res->getBody(), true);
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
