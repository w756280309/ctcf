<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\news\News;
use common\models\category\ItemCategory;
use common\models\category\Category;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * 资讯信息类.
 */
class NewsController extends Controller
{
    use HelpersTrait;

    /**
     * 网站公告,最新资讯,媒体报道列表页面.
     */
    public function actionIndex($type)
    {
        if (!in_array($type, ['info', 'media', 'notice', 'licai', 'touzi'])) {
            throw $this->ex404();
        }
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        $pageSize = 'media' === $type ? 5 : 10;

        $ic = ItemCategory::tableName();
        $n = News::tableName();
        $c = Category::tableName();

        $data = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => $type])
            ->andWhere(['<=', "$n.investLeast", $totalAssets])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC]);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $pageSize]);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        $pagedata = $this->getPage($type,1);

        return $this->render($pagedata['page'], ['model' => $model, 'pages' => $pages, 'type' => $type, 'title' => $pagedata['title']]);
    }

    public function actionDetail($id, $type)
    {
        if (empty($id) || is_int($id) || !in_array($type, ['info', 'media', 'notice', 'licai', 'touzi'])) {
            throw $this->ex404();
        }

        $new = $this->findOr404(News::class, $id);
        if (is_null($new) || $new->status != News::STATUS_PUBLISH) {
            throw $this->ex404();     //不存在的文章或文章隐藏了,抛出404异常
        }
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        if ($new->investLeast > $totalAssets) {
            throw $this->ex404();
        }
        $keys = ArrayHelper::getColumn($new->categories, 'key');

        if (!in_array($type, $keys)) {
            throw $this->ex404();
        }

        $pagedata = $this->getPage($type,2);

        return $this->render($pagedata['page'], ['new' => $new, 'type' => $type, 'title' => $pagedata['title']]);
    }

    private function getPage($type, $pagetype)
    {
        $backdata = array();

        if (empty($type)) {
            throw $this->ex404();
        }

        if ($pagetype === 2) {
            $new_type = $type . 's';
        } else{
            $new_type = $type;
        }

        if ('info' === $type) {
            $backdata['title'] = '最新资讯';
            $backdata['page'] = $new_type;
        } elseif ('media' === $type) {
            $backdata['title'] = '媒体报道';
            $backdata['page'] = ($pagetype === 2) ? 'notices' : 'media';
        } elseif ('licai' === $type) {
            $backdata['title'] = '理财指南';
            $backdata['page'] = ($pagetype === 2) ? 'infos' : 'info';
        } elseif ('touzi' === $type) {
            $backdata['title'] = '投资技巧';
            $backdata['page'] = ($pagetype === 2) ? 'infos' : 'info';
        } else {
            $backdata['title'] = '网站公告';
            $backdata['page'] = $new_type;
        }

        return $backdata;
    }
}
