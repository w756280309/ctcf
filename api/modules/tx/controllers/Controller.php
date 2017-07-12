<?php

namespace api\modules\tx\controllers;

use Tx\UmpClient;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\ContentNegotiator;
use yii\web\BadRequestHttpException;
use yii\web\Controller as BaseController;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Zii\Http\Request;

/**
 * 扩展Yii的Controller类.
 */
class Controller extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'text/html' => Response::FORMAT_HTML,
                ],
            ],
        ];
    }

    /**
     * 获取当前请求对象
     *
     * @return Request
     */
    public function getRequest()
    {
        return Yii::$app->request;
    }

    protected function ex400($message = 'Bad Request', $code = 0, \Exception $previous = null)
    {
        return new BadRequestHttpException($message, $code, $previous);
    }

    protected function ex404($message = null, \Exception $previous = null)
    {
        return new NotFoundHttpException($message, 0, $previous);
    }

    /**
     * 返回对象数据（如果对象没有错误信息，返回所有属性，否则返回对象的所有错误信息）
     *
     * @param ActiveRecord $record
     *
     * @return array
     */
    public function json(ActiveRecord $record)
    {
        if ($record->hasErrors()) {
            \Yii::$app->response->statusCode = 400;

            return $record->getFirstErrors();
        } else {
            return $record->getAttributes();
        }
    }

    //获取请求联动客户端
    public function getUmpClient()
    {
        $umpClient = new UmpClient(Yii::$app->params['ump']);

        return $umpClient;
    }
}
