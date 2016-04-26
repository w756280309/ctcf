<?php

namespace api\modules\v1\controllers\rest;

use common\models\product\OnlineProduct as Loan;
use api\modules\v1\controllers\Controller;

/**
 * 充值交易API.
 */
class LoanController extends Controller
{
    public function actionList()
    {
        $query = Loan::find();

        return $this->paginate($query);
    }

    public function actionGet($id)
    {
        $loan = Loan::findOne($id);
        if (null === $loan) {
            throw $this->ex404();
        }

        return $loan;
    }

    public function actionUmp($id)
    {
        $loan = $this->actionGet($id);
        $resp = \Yii::$container->get('ump')->getLoanInfo($loan->id);

        return [
            'mer_id' => $resp->get('mer_id'),
            'project_account_id' => $resp->get('project_account_id'),
            'project_account_state' => $resp->get('project_account_state'),
            'project_id' => $resp->get('project_id'),
            'project_state' => $resp->get('project_state'),
            'balance' => $resp->get('balance'),
        ];
    }
}
