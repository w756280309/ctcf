<?php

namespace frontend\modules\user\controllers;

use common\models\promo\InviteRecord;
use frontend\controllers\BaseController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;

class InviteController extends BaseController
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
     * 邀请好友页面.
     * 1. 每页显示5条记录;
     * 2. 翻页方式改为Ajax形式;
     */
    public function actionIndex()
    {
        $this->layout = 'main';
        $pageSize = 5;
        $user = $this->getAuthedUser();
        $model = InviteRecord::getInviteRecord($user);

        $data = new ArrayDataProvider([
            'allModels' => $model,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => count($model), 'pageSize' => $pageSize]);

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@frontend/modules/user/views/invite/_list.php', ['model' => $model, 'data' => $data->getModels(), 'pages' => $pages]);
        }

        return $this->render('index', ['model' => $model, 'data' => $data->getModels(), 'user' => $user, 'pages' => $pages]);
    }
}

