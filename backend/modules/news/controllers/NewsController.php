<?php
namespace backend\modules\news\controllers;

use common\models\category\Category;
use common\models\category\ItemCategory;
use Yii;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\news\News;
use yii\helpers\ArrayHelper;
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
        //所有文章分类
        $categories = Category::getTree(News::CATEGORY_TYPE_ARTICLE, 3);
        //状态
        $_statusList = News::getStatusList();
        $_where = [];
        $_andWhere = [];
        $_selectQueryParams = Yii::$app->request->get();
        foreach ($_selectQueryParams as $key => $val) {
            if ($key != 'title' && $key != 'status' && $key != 'home_status' && $key != 'category') {
                unset($_selectQueryParams[$key]);
                continue;
            }
            if ($val !== '') {
                if ($key == 'title') {
                    $_andWhere = ['like', $key, $val];
                } elseif ($key == 'category') {
                    if ($val) {
                        $ids = ItemCategory::getItems([$val], News::CATEGORY_TYPE_ARTICLE);
                        if ($ids) {
                            $_where['id'] = $ids;
                        }
                    }

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
        $models = $query->orderBy(['sort' => SORT_DESC, 'news_time' => SORT_DESC, 'id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'status' => $_statusList,
            'selectQueryParams' => $_selectQueryParams,
            'categories' => ArrayHelper::map($categories, 'id', 'name')
        ]);
    }


    /**
     * Displays a single NewsCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionEdit($id = null)
    {
        //所有文章分类
        $categories = Category::getTree(News::CATEGORY_TYPE_ARTICLE, 3);
        //状态
        $_statusList = News::getStatusList();
        if ($id) {
            $model = $this->findModel($id);
            $model->news_time = date('Y-m-d H:i:s', $model->news_time);
            $item_category = $model->getItemCategories();
            $model->category = $item_category ? ArrayHelper::getColumn($item_category, 'category_id') : [];
            ItemCategory::clearItems([$model->id], News::CATEGORY_TYPE_ARTICLE);
        } else {
            $model = new News();
            $model->creator_id = Yii::$app->user->getId();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $model,
            'status' => $_statusList,
            'categories' => $categories
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
