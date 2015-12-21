<?php
namespace backend\modules\news\controllers;

use Yii;
//use yii\web\Controller;
use yii\data\Pagination;
//use yii\web\UploadedFile;

use backend\controllers\BaseController;

use common\models\news\News;
use common\models\news\NewsCategory;
use common\models\news\NewsFiles;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewsController
 *
 * @author Administrator
 */
class NewsController extends BaseController 
{
    const NEWS_PAGE_SIZE = 10;
    
    
    public function init()
    {
//        parent::init();
//        if (Yii::$app->request->isAjax){
//            Yii::$app->response->format = Response::FORMAT_JSON;
//        }
    }
    
    
    /**
     * Lists all NewsCategory models.
     * @return mixed
     */
    public function actionIndex()
    {   
        //分类
        $_allCategories = NewsCategory::getCategoryTree();
        //状态
        $_statusList = News::getStatusList();
        //首页推荐状态
        $_homeStatusList = News::getHomeStatusList();
        
        $_where=[];
        $_andWhere='';
        $_selectQueryParams = Yii::$app->request->get();     
        //print_r($_GET);
        foreach ($_selectQueryParams as $key => $val){
            if($key != 'title' && $key != 'category_id' && $key != 'status' && $key != 'home_status'){
                unset($_selectQueryParams[$key]);
                continue;
            }
            if($val !== ''){
                if($key == 'title'){
                    $_andWhere = ['like', $key, $val];
                }
                else{
                    $_where[$key] = $val;
                }
            }
        }        
//        print_r($_where);
//        print_r($_andWhere);

        $query = News::find();
        if($_where){
            $query = $query->where($_where);
        }
        if($_andWhere){
            $query = $query->andWhere($_andWhere);
        }
        $query = $query->orderBy('id desc');  
        
        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => static::NEWS_PAGE_SIZE]);        
        $models = $query->offset($pages->offset)->limit($pages->limit)->all();
        //print_r($pages);

        return $this->render('index', ['models' =>$models, 'pages' => $pages, 'categories' => $_allCategories, 'status' => $_statusList, 'homeStatus' => $_homeStatusList, 'selectQueryParams'=>$_selectQueryParams ]);      
    }


    /**
     * Displays a single NewsCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionEdit($id=null)
    {            
        //分类
        $_allCategories = NewsCategory::getCategoryTree();
        //状态
        $_statusList = News::getStatusList();
        //首页推荐状态
        $_homeStatusList = News::getHomeStatusList();
        
        $model=new News();
        if($id) {           
            $model = News::findById($id);
            if(!$model){
                return $this->redirect('/news/default/index');
            }
        }
        else{
            $model->creator_id = Yii::$app->user->getId();
        }
        $files = NewsFiles::find()->where(array("news_id"=>$id))->all();      
        
        if ($model->load(Yii::$app->request->post())&&$model->validate()) {
            $re = $model->save(); 
            if($re){
                NewsFiles::deleteAll(['news_id' => $model->id]);
                $news_files_model = new NewsFiles();
                if($_POST['content']){
                foreach ($_POST['content'] as $key => $val) {
                    $_model = clone $news_files_model;
                    $_model->news_id= $model->id;
                    $_model->content= $val;
                    $_model->save();
                }
                }
            }
            return $this->redirect(['index']);
        }
        if($id){
            $model->news_time = date('Y-m-d H:i:m', $model->news_time);
        }
        return $this->render('edit', ['model' => $model,'files'=>$files, 'categories' => $_allCategories, 'status' => $_statusList, 'homeStatus' => $_homeStatusList]);
    }

    /**
     * Deletes an existing NewsCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    
    public function actionImgdel($id = null, $img = null) {
        if ($id) {
            NewsFiles::deleteAll(['id' => $id]);
        }
        $dr = $_SERVER['DOCUMENT_ROOT'];
        $f = $dr.'/upload/news/'.$img;
        if(file_exists($f)){
            unlink($f);
        }
        echo json_encode(1);
        exit;
    }
    
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
