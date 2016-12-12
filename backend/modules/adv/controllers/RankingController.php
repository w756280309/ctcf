<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\ActiveDataProvider;

class RankingController extends BaseController
{
    /**
     * 活动列表页.
     */
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

    /**
     * 添加活动信息.
     */
    public function actionCreate()
    {
        $model = new RankingPromo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AdminLog::initNew($model)->save(false);

            return $this->redirect('index');
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * 更改活动信息.
     */
    public function actionUpdate($id)
    {
        $model = $this->findOr404(RankingPromo::class, $id);

        $model->startAt = date('Y-m-d H:i:s', $model->startAt);
        $model->endAt = date('Y-m-d H:i:s', $model->endAt);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AdminLog::initNew($model)->save(false);

            return $this->redirect('index');
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * 删除活动.
     */
    public function actionDelete($id)
    {
        $model = $this->findOr404(RankingPromo::class, $id);

        if ($model->delete()) {
            AdminLog::initNew($model)->save(false);
        }

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
        $query = PromoLotteryTicket::find()->where(['isDrawn' => true, 'promo_id' => $promo->id])->andWhere('reward_id is not null')->orderBy(['created_at' => SORT_DESC])->with('user');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('award_list', [
            'promo' => $promo,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 活动上线,下线.
     */
    public function actionOnline($id)
    {
        $promo = $this->findOr404(RankingPromo::class, $id);
        $promo->isOnline = !$promo->isOnline;

        $code = $promo->save(false);

        if ($code) {
            AdminLog::initNew($promo)->save(false);
        }

        return [
            'code' => $code ? 0 : 1,
            'message' => $code ? '操作成功' : '操作失败',
        ];
    }
}