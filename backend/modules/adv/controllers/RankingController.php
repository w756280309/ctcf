<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use wap\modules\promotion\models\RankingPromo;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class RankingController extends BaseController
{
    public function actionIndex()
    {
        $query = RankingPromo::find()->orderBy(['endAt' => SORT_DESC, 'id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort(false);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new RankingPromo();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = RankingPromo::findOne($id);
        if (null === $model) {
            throw new NotFoundHttpException();
        }
        $model->startAt = date('Y-m-d H:i:s', $model->startAt);
        $model->endAt = date('Y-m-d H:i:s', $model->endAt);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = RankingPromo::findOne($id);
        if (null === $model) {
            throw new NotFoundHttpException();
        }
        $model->delete();
        return $this->redirect('index');
    }
}