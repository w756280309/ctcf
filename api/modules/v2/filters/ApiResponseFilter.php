<?php

namespace api\modules\v2\filters;

use Yii;
use yii\base\ActionFilter;

class ApiResponseFilter extends ActionFilter
{
    public function afterAction($action, $result)
    {
        if (
            'application/vnd.uft.mob.legacy+json' === Yii::$app->getRequest()->getHeaders()->get('Accept', '', true)
            && Yii::$app->response->format === 'json'
        ) {
            return [
                'code' => 0,
                'message' => '成功',
                'status' => 'success',
                'data' => [
                    'code' => 0,
                    'msg' => '成功',
                    'result' => 'success',
                    'content' => $result,
                ],
            ];
        }

        return $result;
    }
}
