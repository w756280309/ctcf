<?php
/**
 * Created by IntelliJ IDEA.
 * User: xhy
 * Date: 15-2-3
 * Time: 下午5:14
 */

namespace backend\modules\news\controllers;


use yii\web\Controller;

use backend\controllers\BaseController;

class MainController extends BaseController
{
    public $layout = 'main';

    public function actionIndex()
    {
        return $this->render('index');
    }



}