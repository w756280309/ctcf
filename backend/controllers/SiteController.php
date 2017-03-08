<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;

class SiteController extends BaseController
{
    public $layout = 'frame';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionOpermsg(){
        return $this->render('oper',['res'=>1,'message'=>"恭喜","links"=>array(
                    array('浏览发布的产品','/company/product/update/id/'),
                    array('继续发布产品','/company/product/create/step/1'),
                    )]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionAboutme()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
        if($user){
            return $this->render('aboutme', [
                'model' => $user,
            ]);

        }else{

            return $this->goHome();
        }
    }

    public function actionDeny(){
        return $this->render('deny');
    }

    public function actionError(){
        echo 123;
    }
}
