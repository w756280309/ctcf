<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\promo\InviteRecord;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class InviteController extends BaseController
{
    /**
     * 邀请好友页面.
     */
    public function actionIndex($page = 1)
    {
        $user = $this->getAuthedUser();
        $model = InviteRecord::getInviteRecord($user);
        $pageSize = 5;
        $count = count($model);

        $data = new ArrayDataProvider([
            'allModels' => $model,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $tp = $pages->pageCount;
        $header = [
            'count' => $count,
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/user/views/invite/list.php', ['data' => $data->getModels()]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('index', ['model' => $model, 'data' => $data->getModels(), 'pages' => $pages, 'user' => $user]);
    }
}
