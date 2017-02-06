<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\action\user\PayAgreementAction;

class BindingController extends BaseController
{
    public function actions()
    {
        return [
            'umpmianmi' => PayAgreementAction::className(),//免密
        ];
    }
}
