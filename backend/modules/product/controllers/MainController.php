<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-2-3
 * Time: 下午5:14
 */

namespace backend\modules\product\controllers;

use yii\web\Controller;

class MainController extends Controller
{
    public $layout = 'main';

    public function actionIndex()
    {
        return $this->render('index');
    }
}