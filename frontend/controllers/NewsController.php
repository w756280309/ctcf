<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\news\News;
use common\models\category\ItemCategory;
use common\models\category\Category;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * 资讯信息类.
 */
class NewsController extends Controller
{
    use HelpersTrait;

    public function actionIndex($type)
    {
        $ic = ItemCategory::tableName();
        $n = News::tableName();
        $c = Category::tableName();
        if ($type === "info") {
            $key = \Yii::$app->params['news_key_info'];
            $render = "info";
        } else if ($type === "media") {
            $key = \Yii::$app->params['news_key_media'];
            $render = "media";
        } else if ($type === "notice") {
            $key = \Yii::$app->params['news_key_notice'];
            $render = "notice";
        }
        $data = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => $key])
            ->orderBy(["$n.news_time" => 'desc', "$n.id" => 'desc']);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render($render, ['model' => $model, 'pages' => $pages, 'type'=>$type]);
    }

    public function actionDetail($id, $type)
    {
        if (empty($id) || is_int($id)) {
            throw $this->ex404();
        }
        $new = News::findOne($id);
        if ($type === "info") {
            $render = "infos";
        } else if ($type === "media") {
            $render = "medias";
        } else if ($type === "notice") {
            $render = "notices";
        }
        return $this->render($render, ['new' => $new, 'type'=>$type]);
    }
}
