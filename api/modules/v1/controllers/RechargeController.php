<?php

namespace api\modules\v1\controllers;

use common\models\user\RechargeRecord as Recharge;

/**
 * 充值交易API.
 */
class RechargeController extends Controller
{
    public function actionList()
    {
        $status = null;

        $statusName = $this->getQueryEnum('statusName', Recharge::getStatusNames());
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
