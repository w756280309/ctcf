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
     *
     * @param int $second 秒数 统计几秒之前的数据
     *
     * @return 如果有下单时间超过$second秒而仍为被处理的queue job，就返回400
     */
    public function actionOrder($second)
    {
        if (0 === preg_match("/^\d*$/", $second)) {
            throw new \yii\web\BadRequestHttpException('请求参数异常');
        }
        if (OrderQueue::find()->where('status=0 and created_at<'.(time() - intval($second)))->count()) {
            return Yii::$app->response->statusCode = 400;
        } else {
            return Yii::$app->response->statusCode = 200;
        }
    }
}
