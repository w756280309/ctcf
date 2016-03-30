<?php

namespace api\modules\v1\controllers;

use api\exceptions\InvalidParamException;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\Controller as BaseController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class Controller extends BaseController
{
    public function behaviors()
    {
        return [
            \common\filters\UserAccountAcesssControl::className(),
        ];
    }

    public function init()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function getQueryParam($name)
    {
        return \Yii::$app->request->get($name);
    }

    public function getQueryParamAsInt($name)
    {
        $safe = null;

        $param = \Yii::$app->request->get($name);
        if (null !== $param && is_numeric($param)) {
            $safe = (int) $param;
        }

        return $safe;
    }

    public function getQueryParamAsEnum($name, array $enum)
    {
        $safe = null;

        $param = \Yii::$app->request->get($name);
        if (null !== $param && in_array($param, $enum)) {
            $safe = $param;
        }

        return $safe;
    }

    public function getQueryParamAsBool($name)
    {
        $safe = null;

        $param = \Yii::$app->request->get($name);
        if (null !== $param) {
            $safe = (bool) $param;
        }

        return $safe;
    }

    public function paginate(ActiveQuery $query)
    {
        $count = $query->count();
        $pg = new Pagination(['totalCount' => $count]);

        $page = $this->getQueryParamAsInt('page');
        if (
            null !== $page
            && ($page > $pg->getPageCount() || $page <= 0)
        ) {
            return [];
        }

        return $query->offset($pg->offset)
            ->limit($pg->limit)
            ->all();
    }

    public function exBadParam($paramName, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('参数`%s`取值无效', $paramName);

        return new InvalidParamException($message, $code, $previous);
    }

    public function ex400($message = null, $code = 0, \Exception $prev = null)
    {
        return new BadRequestHttpException($message, $code, $prev);
    }

    public function ex404($message = null, $code = 0, \Exception $prev = null)
    {
        return new NotFoundHttpException($message, $code, $prev);
    }
}
