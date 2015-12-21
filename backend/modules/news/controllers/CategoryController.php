<?php

namespace backend\modules\news\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\controllers\BaseController;
use common\models\news\NewsCategory;

/**
 * CategoryController implements the CRUD actions for NewsCategory model.
 */
class CategoryController extends BaseController
{
    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * Lists all NewsCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        //$pageModels = NewsCategory::getCategoryPageModel(5);
        //return $this->render('index', ['models' => $pageModels['models'], 'pages' => $pageModels['pages']]);
        
        $models = NewsCategory::getCategoryList();   
        
        return $this->render( 'index', ['models'=>$models] );
    }


    /**
     * Displays a single NewsCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionEdit($id=null)
    {
        $_allCategories = NewsCategory::getCategoryTree();
        //print_r($_allCategories);      
        //exit;
        
        $model=new NewsCategory();
        if($id) {           
            $model = NewsCategory::findById($id);
            if(!$model){
                return $this->redirect('/news/default/index');
            }
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();           
        }
        
        return $this->render('edit', ['model' => $model, 'categories' => $_allCategories]);
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

    /**
     * Finds the NewsCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NewsCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewsCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
