<?php

namespace app\modules\news\controllers;

use yii\web\Controller;
use common\models\news\News;

class HelperController extends Controller {

    public $layout = 'helper';

    public function actionIndex($aid=null,$cid=null) {
        if(empty($aid)){
            $aid =  News::find()->where(["category_id"=>$cid])->min('id');
        }
        $news=News::findOne(["id"=>$aid]);
        return $this->render('index',['news' => $news]);
    }

}
