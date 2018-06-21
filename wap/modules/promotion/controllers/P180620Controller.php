<?php

namespace wap\modules\promotion\controllers;

use common\models\product\LoanFinder;
use common\view\ProductLegacyJson;
use yii\data\Pagination;

class P180620Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /*
     * 初始化页面接口
     * */
    public function actionIndex()
    {
        $data = [
            'isLoggedIn' => null !== $this->getAuthedUser(),
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /*
     * 列表数据
     * */
    public function actionList()
    {
        return $this->render('list');
    }

    /*
     * 推荐标的接口
     * */
    public function actionRecommendProduct()
    {
        $expires = (int)\Yii::$app->request->get('expires');
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', 5);

        $query = LoanFinder::queryP2pLoans();
        $query = $query->andWhere(['refund_method' => 10]);
        $query = $query->andWhere(['is_xs' => 0]);
        $query = $query->andWhere(['expires' => $expires]);
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);
        $pages->params = ['page' => $page];
        $totalPage = ceil($count / $size);
        $deals = $query->orderBy([
            'xs_status' => SORT_DESC,
            'recommendTime' => SORT_DESC,
            'sort' => SORT_ASC,
            'raiseDays' => SORT_DESC,
            'finish_rate' => SORT_DESC,
            'raiseSn' => SORT_DESC,
            'isJiaxi' => SORT_ASC,
            'finish_date' => SORT_DESC,
            'id' => SORT_DESC,
        ])->offset($pages->offset)->limit($pages->limit)->all();
        $data = ProductLegacyJson::showProduct($deals);
        $relateInfo = [
            'page' => $page,
            'totalPage' => $totalPage,
            'expires' => $expires,
        ];

        return [
            'code' => 0,
            'data' => $data,
            'relateInfo' => $relateInfo,
        ];
    }
}