<?php

namespace api\modules\v1\controllers\app;

use Yii;
use yii\web\Response;
use api\modules\v1\controllers\Controller;
use common\models\app\AccessToken;

/**
 * App相关api接口
 */
class UserController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 获取用户信息
     */
    public function actionInfo()
    {
        $headers = \Yii::$app->request->headers;

        if (null === $headers['wjftoken']) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "需要登录",
                'data' => null,
            ];
        }

        $accessToken = AccessToken::isEffectiveToken($headers);

        if (null === $accessToken) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "需要登录",
                'data' => null,
            ];
        }

        $user = $accessToken->user;
        if (!$user) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "找不到用户",
                'data' => null,
            ];
        }

        $bank = $user->qpay;
        if ($bank) {
            $bankCard = substr_replace($bank->card_number,'*****',3,-2);
        } else {
            $bankCard = null;
        }

        return [
            'status' => 'success',//程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success',//业务级别成功失败
                'msg' => '成功',
                'content' => [
                    'userid' => '',
                    'asset_total' => '',
                    'profit_balance' => '',
                    'investment_balance' => '',
                    'available_balance' => '',
                    'mobile' => substr_replace($user->mobile,'***', 3, -4),
                    'idcard' => substr_replace($user->idcard,'***', 5, -2),
                    'bankcard' => $bankCard,
                ]
            ]
        ];
    }
}
