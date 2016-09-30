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
            ]);
        } else {
            $respData = Yii::$container->get('txClient')->get('credit-note/user-notes', [
                'user_id' => $user->id,
            ]);
        }

        $respData = empty($respData) ? [] : $respData;

        if (2 === $type) {  //转让中
            foreach ($respData as $key => $note) {
                if ($note['isClosed']) {
                    unset($respData[$key]);
                    continue;
                } else {
                    $respData[$key]['loan'] = Loan::findOne($note['loan_id']);
                }
            }
        } elseif (3 === $type) {  //转让已完成
            foreach ($respData as $key => $note) {
                if ($note['isClosed'] && $note['tradedAmount'] > 0) {
                    $respData[$key]['loan'] = Loan::findOne($note['loan_id']);
                    continue;
                } else {
                    unset($respData[$key]);
                }
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $respData,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => count($respData), 'pageSize' => $pageSize]);
        $data = $provider->getModels();

        if (1 === $type) {   //可转让列表
            foreach ($data as $key => $asset) {
                $data[$key]['loan'] = Loan::findOne($asset['loan_id']);
                $data[$key]['order'] = Order::findOne($asset['order_id']);
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
            $html = $this->renderFile('@wap/modules/credit/views/trade/_more_assets.php', ['data' => $data, 'type' => $type]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('assets', ['data' => $data, 'type' => $type, 'pages' => $pages]);
    }
}
