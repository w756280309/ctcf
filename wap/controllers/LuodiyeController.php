<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use yii;
use yii\web\Controller;
use common\models\user\CaptchaForm;

class LuodiyeController extends Controller
{
    use HelpersTrait;

    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $captcha = new CaptchaForm();
        return $this->render('luodiye', ['captcha' => $captcha]);
    }
}