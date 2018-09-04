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

    /**
     * 是否进行降级处理
     * 区分平台温都、楚天
     * 区分手机ios/android
     */
    public function actionDemotion()
    {
        $version = (int)\Yii::$app->request->get('version');
        $clientType = strtolower(\Yii::$app->request->get('clientType'));
        $platCode = \Yii::$app->params['plat_code'];
        //ios需要降级处理版本号 params-local配置
        $iosVersion = isset(\Yii::$app->params['iosVersion']) ? \Yii::$app->params['iosVersion']: [];
        //android需要降级处理版本号 params-local配置
        $androidVersion = isset(\Yii::$app->params['androidVersion']) ? \Yii::$app->params['androidVersion']: [];
        if (empty($version) || !in_array($clientType, ['ios', 'android'])) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '参数错误',
                'data' => [],
            ];
        }

        $isDemotion = false;
        if ('WDJF' == $platCode) {
            if ('ios' === $clientType) {
                if (in_array($version, $iosVersion)) {
                    $isDemotion = true;
                }
            } else {
                if (in_array($version, $androidVersion)) {
                    $isDemotion = true;
                }
            }
        } else {
            if ('ios' === $clientType) {
                if (in_array($version, $iosVersion)) {
                    $isDemotion = true;
                }
            } else {
                if (in_array($version, $androidVersion)) {
                    $isDemotion = true;
                }
            }
        }

        return [
            'status' => 'success', //程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success', //业务级别成功失败
                'msg' => '成功',
                'content' => [
                    'isDemotion' => $isDemotion,//是否降级
                ],
            ],
        ];
    }
}
