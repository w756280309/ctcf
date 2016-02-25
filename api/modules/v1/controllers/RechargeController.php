<?php

namespace api\modules\v1\controllers;

use common\models\user\RechargeRecord as Recharge;
use yii\web\Response;

/**
 * 充值交易API
 */
class RechargeController extends Controller
{
    const ENDPOINT = 'v1/recharges';

    public function init()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionList()
    {
        $statusName = $this->getQueryEnum('statusName', Recharge::getStatusNames());
        $status = Recharge::getStatusForName($statusName);

        $query = Recharge::find();
        if (null !== $status) {
            $query->andWhere(['status' => $status]);
        }

        return $this->paginate($query);
    }
}
