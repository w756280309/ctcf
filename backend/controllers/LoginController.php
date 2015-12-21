<?php

namespace backend\controllers;

use common\models\adminuser\LoginForm;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class LoginController extends Controller
{
    public $layout = 'login';
    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax)
            Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionIndex()
    {
        
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/');
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }

    }

    public function actionLogout()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }else{
            Yii::$app->user->logout();
            return $this->redirect('/login');
        }

    }

}
