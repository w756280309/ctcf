<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;

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
}
