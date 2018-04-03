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
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        $data = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'allowShowInList' => true])
            ->andWhere(['<=', "investLeast", $totalAssets])
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
        return $this->render('detail', ['new' => $new]);
    }
}
