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
        if (!in_array($type, ['info', 'media', 'notice'])) {
            throw $this->ex404();
        }

        $pageSize = 'media' === $type ? 5 : 10;

        $ic = ItemCategory::tableName();
        $n = News::tableName();
        $c = Category::tableName();

        $data = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => $type])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC]);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $pageSize]);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render($type, ['model' => $model, 'pages' => $pages, 'type' => $type]);
    }

    public function actionDetail($id, $type)
    {
        if (empty($id) || is_int($id) || !in_array($type, ['info', 'media', 'notice'])) {
            throw $this->ex404();
        }

        $new = $this->findOr404(News::class, $id);
        $keys = ArrayHelper::getColumn($new->categories, 'key');

        if (!in_array($type, $keys)) {
            throw $this->ex404();
        }

        if ('info' === $type) {
            $render = 'infos';
        } else {
            $render = 'notices';
        }

        return $this->render($render, ['new' => $new, 'type' => $type]);
    }
}
