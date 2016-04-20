<?php

namespace backend\modules\news\controllers;

use common\models\Category;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use backend\controllers\BaseController;
use common\models\news\NewsCategory;

/**
 * CategoryController implements the CRUD actions for NewsCategory model.
 */
class CategoryController extends BaseController
{

    /**
     * Lists all NewsCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = Category::find()->orderBy(['type' => SORT_ASC, 'parent_id' => SORT_ASC, 'sort' => SORT_DESC])->all();
        return $this->render('index', [
            'models' => $models,
        ]);
    }


    /**
     * Displays a single NewsCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionEdit($id = null)
    {
        $categoryTree = Category::getTree(Category::TYPE_ARTICLE,3);
        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model = Category::initNew();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('edit', [
            'model' => $model,
            'categoryTree' => $categoryTree
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
        $model->status = Category::STATUS_HIDDEN;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (!empty($id) && ($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
