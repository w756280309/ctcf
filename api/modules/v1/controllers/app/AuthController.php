<?php

namespace api\modules\v1\controllers\app;

use Yii;
use yii\web\Response;
use api\modules\v1\controllers\Controller;
use common\models\app\AccessToken;

/**
 * App相关api接口
 */
class AuthController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 退出登录,销毁access token
     */
    public function actionLogout()
    {
        $headers = \Yii::$app->request->headers;

        if (null === $headers['wjftoken']) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "需要登录",
                'data' => null,
            ];
        }

        $accessToken = AccessToken::findOne(['token' => $headers['wjftoken']]);

        if (null === $accessToken) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "需要登录",
                'data' => null,
            ];
        }

        if (\Yii::$app->user->logout()) {
            $accessToken->expireTime = 0;
            $accessToken->save();
        }

        return [
            'status' => 'success',//程序级别成功失败
            'message' => '成功',
            'data' => null,
        ];
    }
}