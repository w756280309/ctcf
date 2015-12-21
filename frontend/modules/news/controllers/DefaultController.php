<?php

namespace app\modules\news\controllers;

use yii\web\Controller;
use common\models\news\NewsCategory;
use common\models\news\News;
use yii\data\Pagination;
use common\models\news\NewsFiles;

class DefaultController extends Controller {

    public $layout = 'main';

    public function actionIndex($cid = null,$nid=null) {
        
        
        $min_id = NewsCategory::find()->min('id');

        $id = $cid ? $cid : $min_id;
        $model = NewsCategory::findOne($id);
        $news_info_model = $nid ? News::findOne([$nid]):new News();
        $news_file_model = $nid ? NewsFiles::find()->where(array('news_id'=>$nid))->all():new NewsFiles();
        $news_data = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>$id]);
        $pages = new Pagination(['totalCount' => $news_data->count(), 'pageSize' => '10']);
        $news_model = $news_data->offset($pages->offset)->limit($pages->limit)->orderBy('news_time desc')->all();
        
        return $this->render('index', ['model' => $model,"news_model"=>$news_model,"pages"=>$pages,'news_info'=>$news_info_model,'filse'=>$news_file_model]);
    }

    public function actionDetail() {
        return $this->render('detail');
    }

}
