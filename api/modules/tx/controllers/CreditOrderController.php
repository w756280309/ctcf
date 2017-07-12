<?php

namespace api\modules\tx\controllers;

use common\models\tx\FinUtils;
use common\models\tx\CreditNote;
use common\models\tx\CreditOrder;
use common\models\tx\UserAsset;

class CreditOrderController extends Controller
{
    /**
     * 新建债权订单接口
     * 请求类型 post
     * 请求参数 ['user_id', 'note_id', 'principal']
     *
     * @return array
     */
    public function actionNew()
    {
        $requestData = json_decode($this->getRequest()->rawBody, true);

        $note = CreditNote::findOne($requestData['note_id']);
        if (null === $note) {
            throw $this->ex404('没有找到债权');
        }
        $asset = $note->asset;
        if (null === $asset) {
            throw new \Exception('没有找到资产');
        }
        $loan = $asset->loan;
        if (null === $loan) {
            throw $this->ex400('没有找到标的');
        }
        if (5 !== $loan->status) {
            throw $this->ex400('没有找到标的');
        }
        if (date('Y-m-d') > $loan->endDate) {
            throw $this->ex400('标的已过期');
        }
        //已结束、已取消债权
        if ($note->isClosed || $note->isCancelled) {
            throw  $this->ex400('转让已结束');
        }
        $config = json_decode($note->config, true);
        //计算应付利息
        $order = $asset->order;
        $interest = FinUtils::calculateCurrentProfit($loan, $requestData['principal'], $order->apr);
        //计算实际支付金额
        $amount = bcmul(bcadd($requestData['principal'], $interest, 14), bcsub(1, bcdiv($note->discountRate, 100, 14), 14), 0);
        //计算手续费
        $fee = bcmul($requestData['principal'], $config['fee_rate'], 0);
        $requestData = array_merge($requestData, [
            'asset_id' => $asset->id,
            'amount' => $amount,
            'interest' => $interest,
            'fee' => $fee,
        ]);

        $order = CreditOrder::initNew();
        if ($order->load($requestData, '') && $order->validateOrder() && $order->save()) {
            return ['id' => $order->id];
        } else {
            $order->status = CreditOrder::STATUS_FAIL;
            $order->save(false);
            return ['id' => $order->id];
        }
    }

    /**
     * 获取订单接口
     * 请求类型 get
     * 请求参数 id
     *
     * @return array
     *
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDetail()
    {
        $id = $this->request->query->getInt('id');
        $order = CreditOrder::findOne($id)->toArray();
        if (null === $order) {
            throw $this->ex404();
        } else {
            $order['currentAsset'] = UserAsset::findOne(['credit_order_id' => $order['id']]);

            return $order;
        }
    }

    /**
     * 获取债权转让订单列表.
     *
     * 1. GET方式请求接口;
     *
     * @param int $page     当前页号
     * @param int $pageSize 一页显示条数
     * @param int $noteId   挂牌记录ID
     */
    public function actionList()
    {
        $requestQuery = $this->request->query;
        $page = $requestQuery->getInt('page', 1);
        $pageSize = $requestQuery->getInt('page_size', 10);
        $noteId = $requestQuery->getInt('id');

        if (empty($noteId)) {
            $this->ex400('参数错误');
        }

        $query = CreditOrder::find()
            ->where(['status' => CreditOrder::STATUS_SUCCESS, 'note_id' => $noteId])
            ->orderBy(['id' => SORT_DESC]);

        $count = $query->count();
        $creditNoteOrders = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->all();

        return [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalCount' => $count,
            'data' => $creditNoteOrders,
        ];
    }

    /**
     * 查询用户的投资信息
     * @param   int     $page       页码
     * @param   int     $page_size  每页条数
     * @param   int     $user_id    用户ID
     * @param   bool    $require_list是否需要返回列表
     * @return array
     * 金额已元为单位返回
     */
    public function actionRecords()
    {
        $query = $this->request->query;
        $page = $query->getInt('page', 1);
        $pageSize = $query->getInt('page_size', 10);
        $userId = $query->getInt('user_id');
        $requireList = $query->getBoolean('require_list', true);

        if (empty($userId)) {
            $this->ex400('参数错误');
        }

        $query = CreditOrder::find()->where(['credit_order.user_id' => $userId]);
        $totalCount = $query->count();
        $successQuery = clone $query;
        $successQuery = $successQuery->andWhere(['credit_order.status' => CreditOrder::STATUS_SUCCESS]);
        $successCount = $successQuery->count();
        $totalInvestAmount = $successQuery->sum('credit_order.principal');
        $errorQuery = $successQuery->andWhere(['credit_order.status' => CreditOrder::STATUS_FAIL]);
        $errorCount = $errorQuery->count();
        if ($requireList) {
            $records = $query
                ->select(['credit_order.id', 'credit_order.principal', 'credit_order.createTime', 'credit_order.status','user_asset.loan_id'])
                ->innerJoin('user_asset', 'credit_order.asset_id=user_asset.id')
                ->offset(($page - 1) * $pageSize)
                ->limit($pageSize)
                ->orderBy(['credit_order.createTime' => SORT_DESC])
                ->asArray()
                ->all();
        } else {
            $records = [];
        }
        $latestOrderTime = CreditOrder::find()->where(['status' => CreditOrder::STATUS_SUCCESS, 'user_id' => $userId])->max('createTime');

        return [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalCount' => $totalCount,
            'successCount' => $successCount,
            'errorCount' => $errorCount,
            'totalInvestAmount' => $totalInvestAmount,
            'data' => $records,
            'latestOrderTime' => $latestOrderTime,
        ];
    }
}
