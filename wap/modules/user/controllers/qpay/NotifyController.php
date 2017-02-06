<?php

namespace app\modules\user\controllers\qpay;

use common\action\notify\BankBackNotifyAction;
use common\action\notify\BankFrontNotifyAction;
use yii\web\Controller;

//绑卡回调
class NotifyController extends Controller
{
    public function actions()
    {
        return [
            'frontend' => BankFrontNotifyAction::className(),
            'backend' => BankBackNotifyAction::className(),
        ];
    }
}
