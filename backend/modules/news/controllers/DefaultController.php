<?php
/**
 * Created by IntelliJ IDEA.
 * User: xhy
 * Date: 15-1-18
 * Time: 下午5:02
 */

namespace backend\modules\news\controllers;


use common\models\News;
use common\models\news\Category;
use common\models\news\Source;
use yii\web\Controller;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

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