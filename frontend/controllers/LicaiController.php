<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\LoanFinder;
use common\models\product\OnlineProduct;
use common\models\user\UserInfo;
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
        $data = LoanFinder::queryPublicLoans();

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);

        $loans = $data->orderBy([
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
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $userIn = UserInfo::findOne(['user_id' => $user->id]);
        }
        if (is_null($user) || $userIn->investTotal < 100000) {
            $jianguan = true;
        } else {
            $jianguan = false;
        }
        $array = [];
        if ($jianguan) {
            $query = OnlineProduct::find()->select('id');
            $query->andWhere(['isLicai' => false]);
            $loans = $query->andWhere("NOT((cid = 2) and if(refund_method = 1, expires > 180, expires > 6))")->asArray()->all();
            foreach ($loans as $v) {
                $array[] = $v['id'];
            }
        }
        $notes = [];
        $totalCount = 0;
        $pageSize = 0;

        $txClient = Yii::$container->get('txClient');
        $response = $txClient->get('credit-note/list', ['page' => $page, 'isCanceled' => false, 'loans' => $array]);

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
