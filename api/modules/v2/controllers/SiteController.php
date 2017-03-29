<?php

namespace api\modules\v2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    //Error Action 错误处理action
    public function actionError()
    {
        $exception = \Yii::$app->getErrorHandler()->exception;
        if (!is_null($exception)) {
            if (
                'application/vnd.uft.mob.legacy+json' === Yii::$app->getRequest()->getHeaders()->get('Accept', '', true)
            ) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'code' => 0,
                    'message' => '成功',
                    'status' => 'success',
                    'data' => [
                        'code' => 1,
                        'msg' => '失败',
                        'result' => 'fail',
                        'content' => [],
                    ],
                ];
            } else {
                throw $exception;
            }
        }
    }
}
