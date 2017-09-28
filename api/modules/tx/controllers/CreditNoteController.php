<?php

namespace api\modules\tx\controllers;

use common\jobs\DingtalkCorpMessageJob;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\tx\CreditNote;
use common\models\tx\CreditOrder;
use common\models\user\User;
use common\models\tx\UserAsset;
use Illuminate\Queue\Capsule\Manager;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * 挂牌记录api
 * Class CreditTradingController.
 */
class CreditNoteController extends Controller
{
    /**
     * 新建挂牌记录
     * 请求为post提交
     * 请求参数为json[
     *      'asset_id' => '用户资产ID',//必要
     *      'amount' => '发起挂牌金额',//必要，已分为单位
     *      'discountRate' => '债权折让率',//非必要
     * ]
     * 返回值json:
     * 成功：{'id' :'新建的挂牌记录主键'}
     *
     * @throws BadRequestHttpException
     *
     * @return mixed
     */
    public function actionNew()
    {
        $requestData = json_decode($this->getRequest()->rawBody, true);
        if (empty($requestData)
            || !isset($requestData['asset_id'])
            || !isset($requestData['amount'])
            || $requestData['amount'] <= 0
        ) {
            throw $this->ex400('参数错误');
        }
        $discountRate = isset($requestData['discountRate']) ? strval($requestData['discountRate']) : '0';
        $asset = UserAsset::findOne($requestData['asset_id']);
        if (!$asset) {
            throw $this->ex404('没有找到指定用户资产');
        }
        if ($asset->isRepaid) {
            throw $this->ex404('用户资产已经完成放款');
        }
        /**
         * @var OnlineProduct $loan
         * @var OnlineOrder $order
         * @var User $user
         */
        $loan = OnlineProduct::findOne($asset->loan_id);
        $order = OnlineOrder::findOne($asset->order_id);
        $user = User::findOne($asset->user_id);
        if (null === $loan || is_null($order) || 5 !== $loan->status) {
            throw $this->ex404('用户资产已经完成放款');
        }
        $note = CreditNote::initNew($asset, $requestData['amount'], $discountRate);
        if ($loan->isTest) {
            $note->isTest = true;
        }
        $transaction = \Yii::$app->db_tx->beginTransaction();
        if ($note->validateCredit() && $note->save()) {
            //新建债权挂牌记录之后更改原资产可转让金额
            $sql = 'UPDATE user_asset SET `isTrading` = 1, `maxTradableAmount` =  `maxTradableAmount` - :amount WHERE id = :id';   //发起债权的时候需要将对应用户资产修改为转让中
            $res = \Yii::$app->db_tx->createCommand($sql, ['amount' => $requestData['amount'], 'id' => $asset->id])->execute();
            if ($res) {
                $transaction->commit();

                //发起转让完成后通知，满足通知条件：新上转让项目，且剩余期限1年以下，转让客户单笔认购金额 >=100万,  预计年化收益率 >= 8.8%
                $remainingDuration = $loan->getRemainingDuration();
                $months = isset($remainingDuration['months']) ? $remainingDuration['months'] : 0;
                $days = $remainingDuration['days'];

                /**
                 * @var Manager $queue
                 */
                $queue = \Yii::$app->queue;
                $message = $user->getName() . "(" . $user->getMobile() . ") 转让了 " . $loan->title . ", 剩余期限";
                if ($months > 0) {
                    $message .= $months . "个月";
                }
                $message .= $days. "天, 预期年化利率" . bcmul($order->yield_rate, 100, 2) . "%, 转让金额" . number_format(bcdiv($note->amount, 100, 2), 2) . "元";
                $job = new DingtalkCorpMessageJob(\Yii::$app->params['ding_notify.user_list.create_note'], $message);
                $queue->push($job);
            } else {
                $transaction->rollBack();
            }
        } else {
            $transaction->rollBack();

            return $this->json($note);
        }
    }

    /**
     * 获取挂牌记录列表
     * 请求为get提交
     * 请求参数为[
     *      'page' => '分页页数',//非必要，默认1
     *      'page_size' => '每页记录个人',//非必要，默认10
     *      'sort' => '排序规则',//非必要，默认isClosed,-createTime
     * ]
     * 返回值json:
     * 成功：[
     *      'page',
     *      'page_size',
     *      'total_count',//查询总条数
     *      'data',//记录数组
     * ]
     *
     * @return array
     */
    public function actionList()
    {
        $requestQuery = $this->request->query;
        $page = $requestQuery->getInt('page', 1);
        $pageSize = $requestQuery->getInt('page_size', 10);
        $getSort = $requestQuery->get('sort', 'isClosed,-createTime');

        $sort = $getSort ? $getSort : 'isClosed,-createTime';
        $responseData = [
            'page' => $page,
            'page_size' => $pageSize,
        ];

        $array = array('amount', 'tradedAmount', 'discountRate', 'isClosed', 'createTime', 'isCancelled');
        $sortCondition = explode(',', $sort);

        $sortArray = array();
        foreach ($sortCondition as $key => $value) {
            if (substr($value, 0, 1) === '-') {
                $keyValue = substr($value, 1);
                $sortArray[$keyValue] = SORT_DESC;
            } else {
                $sortArray[$value] = SORT_ASC;
            }
        }

        if (!empty($sortArray)) {
            foreach ($sortArray as $keyNew => $valueNew) {
                if (!in_array($keyNew, $array)) {
                    unset($sortArray[$keyNew]);
                }
            }
        }

        $query = CreditNote::find()->where(['>', 'tradedAmount', 0])->orWhere(['isClosed' => false])->andWhere(['isTest' => false]);
        $count = $query->count();
        $responseData['total_count'] = $count;
        $creditNoteList = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->orderBy($sortArray)->all();
        $responseData['data'] = $creditNoteList;

        return $responseData;
    }

    /**
     * 获取债权详情.
     *
     * 1. 请求为get方式提交;
     * 2. $isLong 长数据标志位,传了此标志位,函数才会返回预期收益和应付利息;
     *
     * @param int $noteId 挂牌记录ID
     *
     * @throws BadRequestHttpException
     *
     * @return CreditNote全部属性以及UserAsset部分属性
     */
    public function actionDetail()
    {
        $noteId = $this->request->query->getInt('id');
        $isLong = $this->request->query->getBoolean('is_long', false);

        if (!$noteId) {
            throw $this->ex400('参数错误');
        }

        $cr = CreditNote::tableName();

        $model = CreditNote::find()
            ->where(["$cr.id" => $noteId])
            ->innerJoinWith('asset')
            ->one();

        if (null === $model) {
            throw $this->ex404();
        }

        $res = $model->toArray([], ['asset']);

        if ($isLong) {
            $res['remainingInterest'] = $model->remainingInterest;
            $res['currentInterest'] = $model->currentInterest;
        }

        return $res;
    }

    /**
     * 获取用户所有转让中或已转让的挂牌记录.
     *
     * @param int $userId 用户ID
     * @param int $type   交易类型: 2 转让中 3 已转让
     * @param int $offset 查询数据偏移量,默认为0
     * @param int $limit  查询数据单次查询限制条数,默认为10条
     */
    public function actionUserNotes()
    {
        $query = $this->request->query;

        $userId = $query->getInt('user_id');
        $type = $query->getInt('type', 2);
        $offset = $query->getInt('offset', 0);
        $limit = $query->getInt('limit', 10);

        if (!in_array($type, [2, 3])) {
            $type = 2;
        }

        $notes = [];
        if ($userId > 0) {
            $query = CreditNote::find()->where(['user_id' => $userId]);

            if (2 === $type) {
                $query->andWhere(['isClosed' => false]);
            } else {
                $query->andWhere(['isClosed' => true])
                    ->andWhere(['>', 'tradedAmount', 0]);
            }

            $notes = $query
                ->offset($offset)
                ->limit($limit)
                ->orderBy(['id' => SORT_DESC])
                ->all();
        }

        return [
            'offset' => $offset,
            'limit' => $limit,
            'data' => $notes,
        ];
    }

    /**
     * 获取用户转让中或已转让挂牌记录统计数据.
     *
     * @param int $userId 用户ID
     * @param int $type   交易类型: 2 转让中 3 已转让
     *
     * @return array [
     *      'type' => '交易类型',
     *      'totalCount' => '记录总数',
     *      'tradedTotalAmount' => '已转让总金额',
     *      'tradingTotalAmount' => '待转让总金额',
     * ]
     */
    public function actionUserNotesStats()
    {
        $query = $this->request->query;

        $userId = $query->getInt('user_id');
        $type = $query->getInt('type', 2);

        if (!in_array($type, [2, 3])) {
            $type = 2;
        }

        $totalCount = 0;            //总记录条数
        $tradingTotalAmount = 0;    //待转让总金额
        $tradedTotalAmount = 0;     //已转让总金额

        if ($userId > 0) {
            $query = CreditNote::find()->where(['user_id' => $userId]);

            if (2 === $type) {
                $query->andWhere(['isClosed' => false]);
            } else {
                $query->andWhere(['isClosed' => true])
                    ->andWhere(['>', 'tradedAmount', 0]);
            }

            $notes = $query->asArray()->all();

            $totalCount = count($notes);
            $tradedTotalAmount = array_sum(array_column($notes, 'tradedAmount'));
            $tradingTotalAmount = array_sum(array_column($notes, 'amount')) - $tradedTotalAmount;
        }

        return [
            'type' => $type,
            'totalCount' => $totalCount,
            'tradedTotalAmount' => $tradedTotalAmount,
            'tradingTotalAmount' => $tradingTotalAmount,
        ];
    }

    /**
     * 获取挂牌记录对应的实际收入.
     *
     * @param string $ids 挂牌记录ID,格式如1,2,3
     *
     * @return 返回一个包含实际收入信息和挂牌记录ID信息的json,如
     * {
     *     "84":{"note_id":"84","actualIncome":"7416242"},
     *     "85":{"note_id":"85","actualIncome":"201274"},
     *     "86":{"note_id":"86","actualIncome":"3010782"}
     * }
     */
    public function actionActualIncome()
    {
        $query = $this->request->query;
        $ids = explode(',', $query->get('ids'));

        $orders = [];
        if (!empty($ids)) {
            $orders = CreditOrder::find()
                ->select('note_id, sum(amount - fee) as actualIncome')
                ->groupBy('note_id')
                ->where(['note_id' => $ids, 'status' => CreditOrder::STATUS_SUCCESS])
                ->asArray()
                ->all();
        }

        if (!empty($orders)) {
            $orders = ArrayHelper::index($orders, 'note_id');
        }

        return $orders;
    }

    /**
     * 请求为get提交
     * 请求参数为[
     *      'id' => '挂牌记录id',
     * ]
     *
     * @return int 挂牌记录id
     *
     * @throws BadRequestHttpException
     */
    public function actionCancel()
    {
        $id = $this->request->query->getInt('id');
        $note = CreditNote::findOne($id);

        if (empty($id) || null === $note) {
            throw $this->ex400('没有找到该转让信息');
        }

        if ($note->isClosed || $note->isCancelled) {
            throw $this->ex400('转让已结束');
        }

        $note->isCancelled = true;
        $note->cancelTime = date('Y-m-d H:i:s');
        $note->isManualCanceled = true;
        if ($note->save(false)) {
            return $note->id;
        }
        throw $this->ex400('系统繁忙，撤销失败，请稍后重试!');
    }

    /**
     * 获取一个资产的销售所得到的资产
     * 请求为post
     * @return array
     */
    public function actionSoldRes()
    {
        $requestData = json_decode($this->getRequest()->rawBody, true);
        $noteIds = $requestData['credit_note_ids'];
        $res = [];
        foreach ($noteIds as $id) {
            $assets = UserAsset::find()->where(['note_id' => $id])->orderBy(['createTime' => SORT_ASC])->all();
            $res[$id] = $assets;
        }
        return $res;
    }

    /**
     * 获取一个用户的转让数据【所有金额以分为单位】
     * @param   $user_id              int       必要   用户ID
     * @param   $with                 string    必要    需要查询的参数数组，用“,”进行分隔，如 "transfer_count,transfer_sum,list,order"
     * 实例：transfer_count,统计一个用户成功转让次数,该转让有被成功购买记录那么次数加1
     * 实例：transfer_sum ,统计一个用户成功转让总金额
     * 实例：list ,获取一个用户的所有转让列表
     * 实例：order ,是否返回改债权所有成功订单 , 当存在 list 时候有效
     * @param   $page                 int       非必要  页码，默认为1
     * @param   $page_size            int       非必要  分页大小,默认20
     */
    public function actionUser()
    {
        $query = $this->request->query;
        $userId = $query->getInt('user_id');
        $with = $query->get('with');
        $with = explode(',', $with);
        if (empty($with)) {
            throw $this->ex404('查询选项不能为空');
        }
        $withTransferCount = in_array('transfer_count', $with) ? true : false;
        $withTransferSum = in_array('transfer_sum', $with) ? true : false;
        $withList = in_array('list', $with) ? true : false;
        $withOrder = in_array('order', $with) ? true : false;
        $page = $query->getInt('page', 1);
        $pageSize = $query->getInt('page_size', 20);
        $user = User::findOne($userId);
        if (empty($user)) {
            throw $this->ex404('没有找到用户数据');
        }
        $responseData = [];
        if ($withTransferCount) {
            $responseData['transferCount'] = CreditNote::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['>', 'tradedAmount', 0])
                ->count();
        }

        if ($withTransferSum) {
            $responseData['transferSum'] = CreditNote::find()
                ->where(['user_id' => $user->id])
                ->sum('tradedAmount');
        }

        if ($withList) {
            $query = CreditNote::find()->where(['user_id' => $user->id]);
            $responseData['totalNoteCount'] = $query->count();
            $notes = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->orderBy(['id' => SORT_DESC])->all();
            if ($withOrder) {
                foreach ($notes as $key => $note) {
                    $orders = $note->successCreditOrders;
                    $note = $note->getAttributes();
                    $note['creditOrders'] = $orders;
                    $notes[$key] = $note;
                }
            }
            $responseData['noteList'] = $notes;
        }

        return $responseData;
    }
}
