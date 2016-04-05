<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;

/**
 * 资讯信息类
 */
class NewsController extends Controller
{
    use HelpersTrait;

    /**
     * 资讯列表页
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 资讯详情页
     */
    public function actionDetail($id)
    {
        return $this->render('detail', ['id' => $id]);
    }
}
