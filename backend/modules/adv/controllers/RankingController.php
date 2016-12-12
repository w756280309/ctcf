<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\promo\PromoLotteryTicket;
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

    /**
     * 活动的获奖列表
     */
    public function actionAwardList($id)
    {
        $promo = RankingPromo::findOne($id);
        if (empty($promo) || empty($promo->promoClass) || !class_exists($promo->promoClass)) {
            throw $this->ex404('数据未找到');
        }
        $query = PromoLotteryTicket::find()->where(['isDrawn' => true, 'promo_id' => $promo->id])->andWhere('reward_id is not null')->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('award_list', [
            'promo' => $promo,
            'dataProvider' => $dataProvider,
        ]);
    }
}