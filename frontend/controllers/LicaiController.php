<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use GuzzleHttp\Client;
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
            ->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE])
            ->orderBy('recommendTime desc, sort asc, finish_rate desc, id desc');

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $loans = $data->offset($pages->offset)->limit($pages->limit)->all();

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
        $client = new Client(['base_uri' => rtrim(\Yii::$app->params['clientOption']['host']['tx'], '/')]);

        $notes = [];
        $totalCount = 0;
        $pageSize = 0;
        try {
            $response = $client->request('GET', '/Api/credit-note/list', [
                'query' => [
                    'page' => $page,
                    'isCanceled' => false,
                ],
            ]);

            $responseCode = $response->getStatusCode();
            if ($responseCode === 200) {
                $respData = json_decode($response->getBody()->getContents(), true);
                $notes = $respData['data'];
                $totalCount = $respData['total_count'];
                $pageSize = $respData['page_size'];
            }
        } catch(\Exception $ex) {
            //不做任何处理
        }

        foreach ($notes as $key => $note) {
            $loan_id = (int) $note['loan_id'];
            $order_id = (int) $note['order_id'];
            $notes[$key]['loan'] = OnlineProduct::findOne($loan_id);
            $notes[$key]['order'] = OnlineOrder::findOne($order_id);
        }

        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);

        return $this->render('notes', ['notes' => $notes, 'pages' => $pages]);
    }
}
