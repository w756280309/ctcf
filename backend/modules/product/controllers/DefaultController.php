<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-23
 * Time: 下午12:09
 */

namespace backend\modules\product\controllers;


use yii\web\Controller;
use Yii;
use backend\controllers\BaseController;

class DefaultController extends BaseController
{
    public $layout = 'frame';

    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax)
            Yii::$app->response->format = Response::FORMAT_JSON;
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
}