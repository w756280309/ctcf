<?php
namespace backend\modules\news\controllers;

use Yii;
use yii\data\Pagination;
use backend\controllers\BaseController;

use common\models\news\News;
use common\models\news\NewsCategory;
use common\models\news\NewsFiles;
use yii\web\NotFoundHttpException;

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

    /**
     * Lists all NewsCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        //状态
        $_statusList = News::getStatusList();
        $_where = [];
        $_andWhere = '';
        $_selectQueryParams = Yii::$app->request->get();
        foreach ($_selectQueryParams as $key => $val) {
            if ($key != 'title' && $key != 'status' && $key != 'home_status') {
                unset($_selectQueryParams[$key]);
                continue;
            }
            if ($val !== '') {
                if ($key == 'title') {
                    $_andWhere = ['like', $key, $val];
                } else {
                    $_where[$key] = $val;
                }
            }
        }

        $query = News::find();
        if ($_where) {
            $query = $query->where($_where);
        }
        if ($_andWhere) {
            $query = $query->andWhere($_andWhere);
        }
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => static::NEWS_PAGE_SIZE]);
        $models = $query->orderBy('id desc')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'status' => $_statusList,
            'selectQueryParams' => $_selectQueryParams
        ]);
    }


    /**
     * Displays a single NewsCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionEdit($id = null)
    {
        //状态
        $_statusList = News::getStatusList();
        if ($id) {
            $model = $this->findModel($id);
            $model->news_time = date('Y-m-d H:i:s', $model->news_time);
        } else {
            $model = new News();
            $model->creator_id = Yii::$app->user->getId();
        }
       // $files = NewsFiles::find()->where(array("news_id" => $id))->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $model,
            'status' => $_statusList,
        ]);
    }

    /**
     * Deletes an existing NewsCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = News::STATUS_DELETE;
        $model->save(false);

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (!empty($id) && ($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
