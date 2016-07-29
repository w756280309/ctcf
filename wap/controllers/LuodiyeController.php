<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use yii;
use yii\web\Controller;
use common\models\user\CaptchaForm;
use common\models\user\User;

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

    public function actionInvite()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $code = Yii::$app->request->get('code');
        if (empty($code) || null === User::find()->where(['usercode'=>$code, 'status'=>1])->one()) {
            throw $this->ex404();
        }
        Yii::$app->session->set('inviteCode', $code);
        return $this->render('invite');
    }
}