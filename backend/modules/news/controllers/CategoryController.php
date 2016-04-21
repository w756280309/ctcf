<?php

namespace backend\modules\news\controllers;

use common\models\category\Category;
use common\models\news\News;
use Yii;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;

class CategoryController extends BaseController
{

    public function actionIndex()
    {
        $models = Category::find()->orderBy(['type' => SORT_ASC, 'parent_id' => SORT_ASC, 'sort' => SORT_DESC])->all();
        return $this->render('index', [
            'models' => $models,
        ]);
    }

    public function actionEdit($id = null)
    {
        $categoryTree = Category::getTree(News::CATEGORY_TYPE_ARTICLE, 3);
        $model = Category::findOrNew($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('edit', [
            'model' => $model,
            'categoryTree' => $categoryTree,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Category::STATUS_HIDDEN;
        $model->save(false);

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (!empty($id) && ($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
