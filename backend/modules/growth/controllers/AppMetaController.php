<?php

namespace backend\modules\growth\controllers;

use backend\controllers\BaseController;
use common\models\growth\AppMeta;
use Yii;
use yii\data\Pagination;

class AppMetaController extends BaseController
{
    /**
     * AppMeta列表页面
     */
    public function actionIndex()
    {
        $query = AppMeta::find();
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $meta = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();


        return $this->render('index', ['meta' => $meta, 'pages' => $pages]);
    }

    /**
     * 编辑app-meta
     */
    public function actionEdit($id)
    {
        $meta = $this->findOr404(AppMeta::className(), $id);
        if (Yii::$app->request->isPost) {
            if ($meta->load(Yii::$app->request->post()) && $meta->save()) {
                return $this->redirect('/growth/app-meta/index');
            }
        }
        return $this->render('edit', ['meta' => $meta]);
    }
}
