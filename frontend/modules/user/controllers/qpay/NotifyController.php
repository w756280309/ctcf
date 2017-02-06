<?php

namespace frontend\modules\user\controllers\qpay;

use common\action\notify\BankBackNotifyAction;
use common\action\notify\BankFrontNotifyAction;
use frontend\controllers\BaseController;

// 绑卡回调
class NotifyController extends BaseController
{
    public function actions()
    {
        return [
            'frontend' => BankFrontNotifyAction::className(),
            'backend' => BankBackNotifyAction::className(),
        ];
    }
}