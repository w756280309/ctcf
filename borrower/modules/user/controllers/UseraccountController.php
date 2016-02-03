<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Response;
use frontend\controllers\BaseController;

class UseraccountController extends BaseController
{
    public $layout = '@app/views/layouts/main';

    /**
     * 账户中心展示页.
     */
    public function actionAccountcenter()
    {
        return $this->render('accountcenter');
    }
}
