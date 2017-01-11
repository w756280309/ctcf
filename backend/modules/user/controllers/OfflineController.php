<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\models\offline\OfflineUser;
use yii\data\Pagination;

class OfflineController extends BaseController
{
    /**
     * 线下会员列表
     */
    public function actionList()
    {
        $query = OfflineUser::find();
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 15,
        ]);
        $users = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();

        return $this->render('list', ['users' => $users, 'pages' => $pages]);
    }

    /**
     * 线下会员详情页
     */
    public function actionDetail($id)
    {
        $user = OfflineUser::findOne($id);
        if (null === $user) {
            throw $this->ex404();
        }

        return $this->render('detail', ['user' => $user]);
    }
}