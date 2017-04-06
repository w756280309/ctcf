<?php

namespace app\controllers;

use common\models\user\CaptchaForm;
use common\controllers\HelpersTrait;
use common\models\user\User;

use Yii;
use yii\web\Controller;

class LuodiyeController extends Controller
{
    use HelpersTrait;

    /**
     * 落地页注册页.
     */
    public function actionSignup($next = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }
        $captcha = new CaptchaForm();

        return $this->render('luodiye', [
            'captcha' => $captcha,
            'next' => filter_var($next, FILTER_VALIDATE_URL),
        ]);
    }

    public function actionIndex()
    {
        return $this->render('invite', ['isLuodiye' => true]);
    }

    public function actionInvite()
    {
        $code = Yii::$app->request->get('code');
        if (empty($code) || null === User::find()->where(['usercode' => $code, 'status' => 1])->one()) {
            return $this->redirect('index');
        }
        Yii::$app->session->set('inviteCode', $code);

        return $this->render('invite', ['isLuodiye' => false]);
    }
}
