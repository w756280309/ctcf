<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use wap\modules\promotion\models\RankingPromo;
use wap\modules\promotion\models\RankingPromoOfflineSale;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class OfflineSaleController extends BaseController
{
    public function actionIndex()
    {
        $query = RankingPromoOfflineSale::find()->orderBy(['id' => SORT_DESC]);
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 20
        ]);
        $query = $query->offset($pages->offset)->limit($pages->limit);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort(false);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pages' => $pages
        ]);
    }

    public function actionCreate()
    {
        $model = new RankingPromoOfflineSale();
        $ranking = RankingPromo::find()->where(['>=', 'endTime', date('Y-m-d H:i:s')])->orderBy(['id' => SORT_DESC])->all();
        if (0 === count($ranking)) {
            \Yii::$app->session->setFlash('error', '请先添加未过期活动,再为活动录入投资记录。<a href="/adv/ranking/create">添加活动</a>');
            return $this->redirect('index');
        }
        if ($model->load(\Yii::$app->request->post())) {
            $sale = RankingPromoOfflineSale::find()->where(['rankingPromoOfflineSale_id' => $model->rankingPromoOfflineSale_id, 'mobile' => $model->mobile])->one();
            if (null !== $sale && $sale->load(\Yii::$app->request->post()) && $sale->save()) {
                return $this->redirect('index');
            } elseif (null === $sale && $model->save()) {
                return $this->redirect('index');
            }
        }
        return $this->render('_form', [
            'model' => $model,
            'ranking' => $ranking,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = RankingPromoOfflineSale::findOne($id);
        if (null === $model) {
            throw new NotFoundHttpException();
        }
        $ranking = RankingPromo::find()->where(['>=', 'endTime', date('Y-m-d H:i:s')])->orderBy(['id' => SORT_DESC])->all();
        if (0 === count($ranking)) {
            \Yii::$app->session->setFlash('error', '请先添加未过期活动,再为活动录入投资记录。<a href="/adv/ranking/create">添加活动</a>');
            return $this->redirect('index');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }
        return $this->render('_form', [
            'model' => $model,
            'ranking' => $ranking,
        ]);
    }

    public function actionDelete($id)
    {
        $model = RankingPromoOfflineSale::findOne($id);
        if (null === $model) {
            throw new NotFoundHttpException();
        }
        $model->delete();
        return $this->redirect('index');
    }
}