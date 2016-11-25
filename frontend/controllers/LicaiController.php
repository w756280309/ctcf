<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class LicaiController extends Controller
{
    use HelpersTrait;

    /**
     * 我要理财-理财列表页面.
     */
    public function actionIndex()
    {
        $data = OnlineProduct::find()
            ->select('*')
            ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE
            ]);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);

        $loans = $data->orderBy('xs_status desc, recommendTime desc, sort asc, finish_rate desc, id desc')
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', ['loans' => $loans, 'pages' => $pages]);
    }

    /**
     * 我要理财-转让列表页面
     *
     * @param int $page 页码
     *
     * @return mixed
     */
    public function actionNotes($page = 1)
    {
        $notes = [];
        $totalCount = 0;
        $pageSize = 0;

        $txClient = Yii::$container->get('txClient');
        $response = $txClient->get('credit-note/list', ['page' => $page, 'isCanceled' => false]);
        if (null !== $response) {
            $notes = $response['data'];
            $totalCount = $response['total_count'];
            $pageSize = $response['page_size'];


            foreach ($notes as $key => $note) {
                $loan_id = (int) $note['loan_id'];
                $order_id = (int) $note['order_id'];
                $notes[$key]['loan'] = OnlineProduct::findOne($loan_id);
                $notes[$key]['order'] = OnlineOrder::findOne($order_id);
            }
        }

        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);

        return $this->render('notes', ['notes' => $notes, 'pages' => $pages]);
    }
}
