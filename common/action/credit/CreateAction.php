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

        if (Yii::$app->user->isGuest) {
            return ['code' => 1, 'data' => [['msg' => '请登录', 'attribute' => '']]];
        }

        //判断是否存在该资产记录
        $txClient = \Yii::$container->get('txClient');
        if ($asset_id <= 0 || null === ($asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => true]))) {
            return ['code' => 1, 'data' => [['msg' => '不满足转让发起条件', 'attribute' => '']]];
        }

        if ($asset['user_id'] !== (int) Yii::$app->user->identity->id) {
            return ['code' => 1, 'data' => [['msg' => '非本人不能发起该转让', 'attribute' => '']]];
        }

        if ($asset_id > 0 && $amount > 0 && $rate >= 0) {
            try {
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
