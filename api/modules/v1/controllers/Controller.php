<?php

namespace api\modules\v1\controllers;

use api\exceptions\InvalidParamException;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
    public function getQueryInt($name)
    {
        $safe = null;

        $param = \Yii::$app->request->get($name);
        if (null !== $param) {
            $safe = (int) $param;
        }

        return $safe;
    }

    public function getQueryEnum($name, array $enum)
    {
        $safe = null;

        $param = \Yii::$app->request->get($name);
        if (null !== $param && in_array($param, $enum)) {
            $safe = $param;
        }

        return $safe;
    }

    public function getQueryBool($name)
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

        $page = $this->getQueryInt('page');
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

    public function createInvalidParamException($endpoint, $paramName, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('`%s`API的`%s`参数取值无效', $endpoint, $paramName);

        return new InvalidParamException($message, $code, $previous);
    }
}
