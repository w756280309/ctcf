<?php

namespace backend\modules\growth\controllers;

use backend\controllers\BaseController;
use common\models\growth\Referral;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ReferralController extends BaseController
{
    /**
     * CampaignSource列表页面
     */
    public function actionIndex()
    {
        $request = Yii::$app->request->get();
        $query = Referral::find()->orderBy(['id' => SORT_DESC]);
        if ($request['name']) {
            $query->andFilterWhere(['like', 'name', $request['name']]);
        }
        if ($request['code']) {
            $query->andFilterWhere(['like', 'code', $request['code']]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 编辑CampaignSource
     */
    public function actionEdit($id)
    {
        $model = $this->findOr404(Referral::className(), $id);
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect('index');
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * 增加CampaignSource记录
     */
    public function actionAdd()
    {
        $model = new Referral();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }
        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * 删除渠道码
     */
    public function actionDelete($id)
    {
        $model = $this->findOr404(Referral::className(), $id);
        $model->delete();
        return $this->redirect('index');
    }

}
