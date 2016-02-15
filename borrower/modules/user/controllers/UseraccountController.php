<?php

namespace app\modules\user\controllers;

use frontend\controllers\BaseController;
use common\models\user\UserAccount;

class UseraccountController extends BaseController
{

    public $layout = '@app/views/layouts/main';

    /**
     * 账户中心展示页.
     */
    public function actionAccountcenter()
    {
        $model = UserAccount::findOne(['uid' => $this->user->id]);
        return $this->render('accountcenter', ['model' => $model]);
    }

}
