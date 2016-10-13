<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\user\User;
use Yii;
use yii\web\Controller;

class LuodiyeController extends Controller
{
    use HelpersTrait;

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
