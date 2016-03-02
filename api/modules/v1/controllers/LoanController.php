<?php

namespace api\modules\v1\controllers;

use common\models\product\OnlineProduct as Loan;

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
}
