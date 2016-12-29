<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\news\News;
use Yii;
use yii\web\Controller;

/**
 * 资讯信息类.
 */
class NewsController extends Controller
{
    use HelpersTrait;

    /**
     * 资讯列表页.
     */
    public function actionIndex($page = 1, $size = 10)
    {
        $data = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'allowShowInList' => true])
            ->orderBy(['news_time' => SORT_DESC]);

        $pg = Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('index', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    /**
     * 资讯详情页.
     */
    public function actionDetail($id)
    {
        if (empty($id) || is_int($id)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $new = News::findOne($id);

        return $this->render('detail', ['new' => $new]);
    }
}
