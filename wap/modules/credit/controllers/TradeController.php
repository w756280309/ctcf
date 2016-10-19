<?php

namespace app\modules\credit\controllers;

use app\controllers\BaseController;
use common\models\order\OnlineOrder as Order;
use common\models\product\OnlineProduct as Loan;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class TradeController extends BaseController
{
    public function actions()
    {
        return [
            'cancel' => 'common\action\credit\CancelAction',
        ];
    }

    /**
     * 债权转让列表.
     *
     * 1. 一页显示5条记录;
     */
    public function actionAssets($type = 1, $page = 1)
    {
        $type = intval($type);

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }

        $user = $this->getAuthedUser();
        $pageSize = 5;

        if (1 === $type) {   //可转让
            $respData = Yii::$container->get('txClient')->get('assets/transferable-list', [
                'user_id' => $user->id,
                'offset' => ($page - 1) * $pageSize,
                'limit' => $pageSize,
            ]);

            $pages = new Pagination(['totalCount' => $respData['totalCount'], 'pageSize' => $pageSize]);
        } else {
            $stats = Yii::$container->get('txClient')->get('credit-note/user-notes-stats', [
                'user_id' => $user->id,
                'type' => $type,
            ]);

            $pages = new Pagination(['totalCount' => $stats['totalCount'], 'pageSize' => $pageSize]);

            $respData = Yii::$container->get('txClient')->get('credit-note/user-notes', [
                'user_id' => $user->id,
                'type' => $type,
                'offset' => $pages->offset,
                'limit' => $pages->limit,
            ]);
        }

        $data = $respData['data'];

        foreach ($data as $key => $asset) {
            $data[$key]['loan'] = Loan::findOne($asset['loan_id']);

            if (1 === $type) {   //可转让列表
                $data[$key]['order'] = Order::findOne($asset['order_id']);
            }
        }

        $actualIncome = [];
        if (3 === $type) {
            if (!empty($data)) {
                $ids = implode(',', array_column($data, 'id'));

                $actualIncome = Yii::$container->get('txClient')->get('credit-note/actual-income', [
                    'ids' => $ids,
                ]);
            }
        }

        $tp = $pages->pageCount;
        $header = [
            'count' => $pages->totalCount,
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/credit/views/trade/_more_assets.php', ['data' => $data, 'type' => $type, 'actualIncome' => $actualIncome]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('assets', ['data' => $data, 'type' => $type, 'pages' => $pages, 'actualIncome' => $actualIncome]);
    }
}
