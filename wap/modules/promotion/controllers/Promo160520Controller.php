<?php

namespace wap\modules\promotion\controllers;

use Exception;
use wap\modules\promotion\models\Promo160520;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class Promo160520Controller extends Controller
{
    const START_TIME = '2016-05-20 10:00:00';//活动开始时间
    const END_TIME = '2016-06-20 23:59:59';//活动结束时间

    private function isStart()
    {
        return time() > strtotime(self::START_TIME);
    }

    private function isEnd()
    {
        return time() > strtotime(self::END_TIME);
    }

    public function actionIndex()
    {
    }

    public function actionDraw($mobile)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (!Promo160520::isValidMobile($mobile)) {
                throw new BadRequestHttpException('无效的手机号');
            }
            if (!$this->isStart()) {
                throw new BadRequestHttpException('活动未开始');
            }
            if ($this->isEnd()) { //活动结束
            }

            Promo160520::checkDraw($mobile);
            if (!Promo160520::draw($mobile)) {
                throw new Exception('领取失败');
            }

            Yii::$app->response->statusCode = 200;

            return [
                'message' => '',
            ];
        } catch (BadRequestHttpException $exc) {
            Yii::$app->response->statusCode = 400;

            return [
                'message' => $exc->getMessage(),
            ];
        } catch (Exception $exc) {
            Yii::$app->response->statusCode = 500;

            return [
                'message' => $exc->getMessage(),
            ];
        }
    }
}
