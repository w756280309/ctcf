<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-18
 * Time: 下午5:02
 */

namespace backend\modules\user\controllers;



use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class DefaultController extends \backend\controllers\BaseController
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