<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\order\OrderQueue;

/**
 * 监控 controller.
 */
class MonitorController extends Controller
{
    /**
     * 用于统计$second之前的数据是否存在未处理的订单.
     */
    public function actionOrder($second)
    {
        if (0 === preg_match("/^\d*$/", $second)) {
            throw new \yii\web\BadRequestHttpException('请求参数异常');
        }
        if (OrderQueue::find()->where('status=0 and created_at<'.(time() - intval($second)))->count()) {
            Yii::$app->response->statusCode = 400;
        } else {
            Yii::$app->response->statusCode = 200;
        }
    }
}
