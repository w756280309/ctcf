<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use Yii;
use yii\web\Controller;

class LicaiController extends Controller
{
    use HelpersTrait;

    public function actionNotes($page = 1)
    {
        $notes = [];
        $tp = 0;
        $txClient = Yii::$container->get('txClient');
        $response = $txClient->get('credit-note/list', ['page' => $page, 'page_size' => 5, 'isCanceled' => false]);
        if (null !== $response) {
            $notes = $response['data'];

            foreach ($notes as $key => $note) {
                $loan_id = (int) $note['loan_id'];
                $order_id = (int) $note['order_id'];
                $notes[$key]['loan'] = OnlineProduct::findOne($loan_id);
                $notes[$key]['order'] = OnlineOrder::findOne($order_id);
            }

            $tp = ceil($response['total_count'] / $response['page_size']);
            $header = [
                'count' => $response['total_count'],
                'size' => $response['page_size'],
                'tp' => $tp,
                'cp' => $response['page'],
            ];
            $code = ($page > $tp) ? 1 : 0;
            $message = ($page > $tp) ? '数据错误' : '消息返回';
        }

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/views/licai/_more_note.php', ['notes' => $notes]);
            return ['header' => $header, 'code' => $code, 'message' => $message, 'notes' => $notes, 'html' => $html];
        }

        return $this->render('notes', ['notes' => $notes, 'tp' => $tp]);
    }
}
