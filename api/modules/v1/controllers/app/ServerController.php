<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\app\AccessToken;

/**
 * App相关api接口
 */
class ServerController extends Controller
{
    /**
     * 获取服务器时间
     */
    public function actionTimestamp()
    {
        return [
            'status' => 'success',//程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success',//业务级别成功失败
                'msg' => '成功',
                'content' => [
                    'serverts' => time(),
                ]
            ]
        ];
    }

    /**
     * 获取token相关信息
     */
    public function actionTokeninfo($key)
    {
        $accessToken = AccessToken::findOne(['token' => $key]);

        if (!$accessToken) {
            return [
                'status' => "fail",//程序级别成功失败
                'message' => "找不到token信息",
                'data' => null,
            ];
        }

        return [
            'status' => 'success',//程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success',//业务级别成功失败
                'msg' => '成功',
                'content' => [
                    'uid' => $accessToken->uid,
                    'mobile' => $accessToken->user->mobile,
                    'expires_at' => date('Y-m-d H:i:s', $accessToken->expireTime),    //过期时间
                    'created_at' => date('Y-m-d H:i:s', $accessToken->create_time),   //创建时间
                    'queried_at' => date('Y-m-d H:i:s'),  //查询时间
                    'is_valid' => $accessToken->expireTime >= time(),
                ]
            ]
        ];
    }
}
