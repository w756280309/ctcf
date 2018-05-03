<?php

namespace api\modules\njq\controllers;

use common\models\transfer\TransferTx;
use Njq\Crypto;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class TransferController extends Controller
{
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 参数：
     *
     * - 业务流水号（sn）
     * - 平台ID platformId
     * - 版本号 version
     * - 请求时间 requestTime
     * - 签名（sign）
     *
     * [
     *     'code' => 2000,
     *     'message' => '成功',
     *     'data' => ,
     * ]
     */
    public function actionCheck()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        $sn = trim($request->get('sn'));

        //验签
        $crypto = new Crypto();
        if (!$crypto->verifySign($params) || '' === $sn) {
            return [
                'code' => 2001,
                'message' => '参数错误',
                'data' => [],
            ];
        }

        //判断订单是否存在
        $transferTx = TransferTx::findOne(['ref_sn' => $sn]);
        if (null === $transferTx) {
            return [
                'code' => 2002,
                'message' => '订单不存在',
                'data' => [],
            ];
        }

        return [
            'code' => 2000,
            'message' => '查询成功',
            'data' => [
                'status' => $transferTx->status,
                'label' => $transferTx->getStatusLabel(),
            ],
        ];
    }
}
