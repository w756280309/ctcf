<?php

namespace api\modules\v1\controllers\rest;

use common\models\user\RechargeRecord as Recharge;
use api\modules\v1\controllers\Controller;

/**
 * 充值交易API.
 */
class RechargeController extends Controller
{
    public function actionList()
    {
        $status = null;

        $statusName = $this->getQueryParamAsEnum('status_name', Recharge::getStatusNames());
        if (null !== $statusName) {
            $status = Recharge::getStatusForName($statusName);
        }

        $query = Recharge::find();
        if (null !== $status) {
            $query->andWhere(['status' => $status]);
        }

        return $this->paginate($query);
    }
}
