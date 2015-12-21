<?php

namespace app\modules\news\controllers;

use yii\web\Controller;

class DefaultController extends Controller {

    //public $layout='main';
    public function actionIndex($cid = null,$nid=null) {
        return $this->render('index');
    }

    public function actionDetail() {
        return $this->render('detail');
    }

}
