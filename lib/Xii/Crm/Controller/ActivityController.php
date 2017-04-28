<?php

namespace Xii\Crm\Controller;


use Xii\Crm\Model\Account;
use Xii\Crm\Model\Activity;
use Xii\Crm\Model\ActivityNoteForm;
use Xii\Crm\Model\Contact;
use Xii\Crm\Model\Identity;
use Xii\Crm\Model\Engagement;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

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

    /**
     * 客服记录
     *
     * 1. 能够展示客服记录(activity表)，每页15条记录
     * 2. 能够为用户添加备注
     * 3. 添加完之后任然返回该客服记录表
     * 4. 客服记录按照创建时间倒叙
     */
    public function actionIndex($accountId)
    {
        $account = Account::findOne($accountId);
        if (is_null($account)) {
            return $this->redirect('/crm/account');
        }
        $query = Activity::find()->where(['account_id' => $account->id])->orderBy(['createTime' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
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
                    'summary' => $model->summary,
                ]);
                $activity->save(false);
                return $this->redirect('/crm/activity/index?accountId=' . $accountId);
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

    /**
     * 根据电话号码获取用户姓名
     *
     * @param $number
     * @return array
     */
    public function actionInfo($number)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $contact = Contact::fetchOneByNumber($number);
        if (is_null($contact)) {
            $identity = null;
        } else {
            $identity = Identity::findOne(['account_id' => $contact->account_id]);
        }

        return [
            'account_id' => is_null($contact) ? null : $contact->account_id,
            'name' => is_null($identity) ? null : $identity->getName(),
        ];
    }


    /**
     * 添加电话记录
     *
     * 1. 联系方式改变时候，请求后台匹配Contact，获取用户Identity的姓名
     * 2. 保存时候如果匹配到了Contact, 需要添加一条phone_call 和　一条　activity
     * 3. 如果没有匹配到Contact, 需要额外新建Account\ Contact\Identity
     * 4. 在客服记录上添加性别
     */
    public function actionCall()
    {
        $engagement = new Engagement([
            'callTime' => date('Y-m-d H:i:s'),
            'direction' => Engagement::TYPE_IN,
            'gender' =>Engagement::GENDER_MALE,
            'creator_id' => \Yii::$app->getUser()->getIdentity()->getId(),
            'activityType' => Activity::TYPE_PHONE_CALL,
        ]);

        if (
            $engagement->load(\Yii::$app->request->post())
            && $engagement->validate()
            && $engagement->addCall()
        ) {
            return $this->redirect('/crm/activity/index?accountId=' . $engagement->account_id);
        }

        return $this->render('call', [
            'engagement' => $engagement,
        ]);
    }
}