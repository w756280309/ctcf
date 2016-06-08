<?php
namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\filters\AccessControl;
use yii\web\Controller;

class BaseController extends Controller
{
    use HelpersTrait;

    protected $user;

    public function init()
    {
        error_reporting(E_ALL ^ E_NOTICE);

        if (!\Yii::$app->user->isGuest) {
            $this->user = \Yii::$app->user->getIdentity();
        }

        parent::init();
    }
}
