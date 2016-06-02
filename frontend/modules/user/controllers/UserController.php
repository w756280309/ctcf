<?php

namespace frontend\modules\user\controllers;

use common\models\user\MoneyRecord;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;

class UserController extends BaseController
{
    public $layout = 'main';
    public function actionMingxi()
    {
        $query = MoneyRecord::find()->select(['created_at', 'type', 'in_money', 'out_money', 'balance', 'osn'])->where(['uid' => Yii::$app->user->identity->id])->andWhere(['in', 'type', MoneyRecord::getLenderMrType()]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 10]);
        $query = $query->orderBy(['created_at' => SORT_DESC])->offset($pages->offset)->limit($pages->limit);
        $lists = $query->all();
        return $this->render('mingxi', [
            'pages' => $pages,
            'lists' => $lists
        ]);
    }
}