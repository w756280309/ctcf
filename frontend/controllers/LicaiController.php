<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\product\OnlineProduct;
use yii\data\Pagination;
use yii\web\Controller;

class LicaiController extends Controller
{
    use HelpersTrait;

    /**
     * 我要理财页面.
     */
    public function actionIndex()
    {
        $data = OnlineProduct::find()
            ->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE])
            ->orderBy('recommendTime desc, sort asc, finish_rate desc, id desc');

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $loans = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', ['loans' => $loans, 'pages' => $pages]);
    }
}