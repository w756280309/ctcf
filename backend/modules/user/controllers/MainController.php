<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-2-3
 * Time: 下午5:14
 */

namespace backend\modules\user\controllers;

class MainController extends \backend\controllers\BaseController
{
    public $layout = 'main';

    public function actionIndex()
    {
        return $this->render('index');
    }
}