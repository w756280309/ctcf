<?php

namespace app\modules\news\controllers;

use yii\web\Controller;
use common\models\news\News;

class AboutController extends Controller {

    public $layout = 'about';

    public function actionIndex($aid=null,$cid=null) {
        if(empty($aid)){
            $aid =  News::find()->where(["category_id"=>$cid])->min('id');
        }
        $news=News::findOne(["id"=>$aid]);
        return $this->render('index', ['news' => $news]);
    }

    public function actionOrg($current = null) {
        return $this->render('org', ['current' => $current]);
    }

    public function actionFriend($current = null) {
        return $this->render('friend', ['current' => $current]);
    }

    public function actionJob($current = null) {
        return $this->render('job', ['current' => $current]);
    }

    public function actionContact($current = null) {
        return $this->render('contact', ['current' => $current]);
    }

}
