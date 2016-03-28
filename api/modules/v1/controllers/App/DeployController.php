<?php

namespace api\modules\v1\controllers\app;

use Yii;
use yii\web\Response;
use api\modules\v1\controllers\Controller;

/**
 * App相关api接口
 */
class DeployController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 版本更新信息
     */
    public function actionAppver($version)
    {
        return [
            'status' => 'success',//程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success',//业务级别成功失败
                'msg' => '成功',
                'content' => [

                ]
            ]
        ];
    }
}
