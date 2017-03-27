<?php

namespace frontend\modules\mall\controllers;

use common\models\mall\PointRecord;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class PointController extends BaseController
{
    public $layout = '@frontend/modules/user/views/layouts/main.php';

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
     * 我的积分.
     */
    public function actionIndex()
    {
        $user = $this->getAuthedUser();

        $query = PointRecord::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['isOffline' => false]);

        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 10,
        ]);

        $points = $query
            ->limit($pages->limit)
            ->offset($pages->offset)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'user' => $user,
            'points' => $points,
            'pages' => $pages,
        ]);
    }

    /**
     * 积分规则.
     */
    public function actionRules()
    {
        return $this->render('rules');
    }
}