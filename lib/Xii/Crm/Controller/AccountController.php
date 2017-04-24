<?php

namespace Xii\Crm\Controller;


use yii\web\Controller;

class AccountController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}