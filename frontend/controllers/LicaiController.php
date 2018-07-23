<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\LoanFinder;
use common\models\product\OnlineProduct;
use common\models\tx\CreditNote;
use common\models\user\UserAccount;
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
        $data = $data->andWhere('cid != 3');

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
        //未登录或者未投资的用户不可见
        if (
            Yii::$app->params['plat_code'] == 'WDJF'
            && (empty($user) || ($user->orderCount() <= 0 && $user->creditOrderCount() <= 0))
        ) {
            throw $this->ex404();
        }
        if (is_null($user) || (null !== $user && $user->getJGMoney() < 50000)) {
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

        //获得所有可见的转让的id
        $userId = null === $user ? null : $user->id;
        $noteIds = CreditNote::getVisibleTradingIds($userId);
        $notLoanIds = [];
        if (null !== $user && $user->getJGMoney() < 50000) {
            $notLoanIds = OnlineProduct::find()
                ->select('id')
                ->where(['!=', 'cid', 3])
                ->column();
        }

        $notes = [];
        $totalCount = 0;
        $pageSize = 0;

        $txClient = Yii::$container->get('txClient');
        $response = $txClient->post('credit-note/list', ['page' => $page, 'isCanceled' => false, 'loans' => $array, 'noteIds' => $noteIds, 'notLoanIds' => $notLoanIds]);

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

    /**
     * 网贷列表
     */
    public function actionLoan()
    {
        $data = LoanFinder::queryP2pLoans();

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
}
