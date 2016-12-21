<?php

namespace backend\modules\growth\controllers;

use backend\controllers\BaseController;
use common\models\growth\PageMeta;
use Yii;
use yii\data\Pagination;
use yii\helpers\Html;

class PageMetaController extends BaseController
{
    /**
     * Meta列表页面
     */
    public function actionList()
    {
        $query = PageMeta::find();
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $meta = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();

        return $this->render('list', ['meta' => $meta, 'pages' => $pages]);
    }

    /**
     * 添加Meta
     */
    public function actionAdd()
    {
        $meta = new PageMeta();

        if ($meta->load(Yii::$app->request->post())
            && ($meta = $this->exchangeMeta($meta))
            && $meta->validate()
            && $meta->save()
        ) {
            $this->redirect('/growth/page-meta/list');
        }

        return $this->render('edit', ['meta' => $meta]);
    }

    /**
     * 编辑Meta信息
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $meta = $this->findOr404(PageMeta::class, $id);

        if ($meta->load(Yii::$app->request->post())
            && $meta->validate()
            && ($meta = $this->exchangeMeta($meta))
            && $meta->save()
        ) {
            $this->redirect('/growth/page-meta/list');
        }

        return $this->render('edit', ['meta' => $meta]);
    }

    private function exchangeMeta(PageMeta $meta)
    {
        $meta->url = trim(Html::encode($meta->url), '?');
        $meta->url = trim($meta->url, '\/');
        $meta->href = $meta->url;
        if (false !== strpos($meta->url, '?')) {
            $metaArr = explode('?', $meta->url);
            $meta->url = $metaArr[0];
        }

        return $meta;
    }
}
