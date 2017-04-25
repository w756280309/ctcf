<?php

namespace Xii\Crm\Controller;


use Xii\Crm\Model\Account;
use Xii\Crm\Model\Activity;
use Xii\Crm\Model\ActivityNoteForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class ActivityController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    //客服记录
    public function actionIndex($accountId)
    {
        $account = Account::findOne($accountId);
        if (is_null($account)) {
            return $this->redirect('/crm/account');
        }
        $query = Activity::find()->where(['account_id' => $account->id])->orderBy(['createTime' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>false,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $model = new ActivityNoteForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            try {
                $activity = new Activity([
                    'account_id' => $account->id,
                    'creator_id' => \Yii::$app->user->getIdentity()->getId(),
                    'type' => Activity::TYPE_NOTE,
                    'content' => $model->content,
                ]);
                $activity->save(false);
                return $this->redirect('/crm/activity/index?accountId='.$accountId);
            } catch (\Exception $e) {
                $model->addError('contact', $e->getMessage());
            }
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'account' => $account,
            'model' => $model,
        ]);
    }
}