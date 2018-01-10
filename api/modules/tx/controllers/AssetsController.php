<?php

namespace api\modules\tx\controllers;

use common\models\tx\CreditNote;
use common\models\tx\CreditOrder;
use common\models\tx\Loan;
use common\models\order\OnlineRepaymentPlan as Plan;
use common\models\tx\Order;
use common\models\tx\UserAsset;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class AssetsController extends Controller
{
    /**
     * 标的成立时,记录资产信息.
     *
     * 1. 请求为post请求;
     * 2. 请求的数据为json格式的字符串,具体格式如下:
     *  [
     *      {
     *          "user_id": 1,
     *          "order_id": 1,
     *          "loan_id": 1,
     *          "amount": 3000,
     *          "orderTime": "时间",
     *          "isTest": 是否为测试标的,
     *          "allowTransfer":true,
     *      },
     *      {
     *          "user_id": 1,
     *          "order_id": 2,
     *          "loan_id": 1,
     *          "amount": 6000,
     *          "orderTime": "时间",
     *          "isTest": 是否为测试标的,
     *          "allowTransfer":true,
     *      }
     *  ]
     * 3. 唯有全部记录成功,才返回成功;
     * 4. 其他错误,一律报400;
     */
    public function actionRecord()
    {
        $assetRecords = json_decode($this->request->rawBody, true);

        if (empty($assetRecords)) {
            throw $this->ex400('请求的数据不能为空');
        }

        $transaction = Yii::$app->db_tx->beginTransaction();

        try {
            foreach ($assetRecords as $assetRecord) {
                $asset = UserAsset::initNew();
                $asset->setAttributes($assetRecord);
                $asset->maxTradableAmount = $asset->amount;
                $asset->isTest = $assetRecord['isTest'];

                if ($asset->save()) {
                    $resultArr[] = $asset;
                } else {
                    throw new \Exception();
                }
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        return $resultArr;
    }

    /**
     * 修改用户资产状态为已回款状态.
     *
     * 1. 请求方式为post请求;
     * 2. 请求参数为json格式的数据:
     *    {
     *      "loan_id" : 标的ID
     *    }
     * 3. 返回影响记录的条数;
     */
    public function actionUpdateRepaidStatus()
    {
        $request = json_decode($this->request->rawBody, true);

        if (empty($request)) {
            throw $this->ex400('请求的数据不能为空');
        }

        $loan = Loan::findOne(['id' => $request['loan_id'], 'status' => 6]);    //判断标的的状态是否为已还清

        if (null === $loan) {
            throw $this->ex400('标的不是已还清项目');
        }

        CreditNote::updateAll(['isClosed' => true], [
            'loan_id' => $loan->id,
            'isClosed' => false,
        ]);

        return UserAsset::updateAll(['isRepaid' => true], ['loan_id' => $loan->id]);
    }

    //获取用户资产详情
    public function actionDetail()
    {
        $asset_id = $this->request->query->getInt('id');
        $validate = $this->request->query->getBoolean('validate');
        $asset = UserAsset::findOne($asset_id);
        if (null === $asset) {
            throw $this->ex404();
        } else {
            $data = $asset->getAttributes();
            $data['minOrderAmount'] = min($asset->maxTradableAmount, $asset->loan->minOrderAmount);
            $data['incrOrderAmount'] = $asset->loan->incrOrderAmount;

            if ($validate) {
                $data['validate'] = $asset->canBuildCredit();
            }

            return $data;
        }
    }

    /**
     * 获取用户所有收益中或已还清资产列表.
     *
     * 1. 每页显示10条记录;
     * 2. 排序以订单ID的倒序排列;
     * 3. GET方式请求;
     *
     * @param int $userId   用户ID
     * @param int $type     状态标识位: 1 收益中 3 已还清
     * @param int $page     当前页号
     * @param int $pageSize 一页显示条数
     */
    public function actionList()
    {
        $query = $this->request->query;
        $userId = $query->getInt('user_id');
        $type = $query->getInt('type');
        $page = $query->getInt('page', 1);
        $pageSize = $query->getInt('page_size', 10);

        if (!in_array($type, [1, 3])) {
            throw $this->ex400('参数错误');
        }

        $assetsData = [];
        if ($userId > 0) {
            $co = CreditOrder::tableName();
            $ua = UserAsset::tableName();
            $assetQuery = UserAsset::find()
                ->select("$ua.*, $co.createTime as txOrderTime")
                ->leftJoin($co, "$co.id = $ua.credit_order_id")
                ->where(["$ua.user_id" => $userId]);

            if (1 === $type) {  //收益中
                $assetQuery->andWhere(["$ua.isRepaid" => false]);
                $assetQuery->andWhere(['>', "$ua.amount", 0]);
            } elseif (3 === $type) {    //已还清
                $assetQuery->andWhere(["$ua.isRepaid" => true, "$ua.isInvalid" => false]);
            }

            $assets = $assetQuery->asArray()->all();

            if (!empty($assets)) {
                $loansId = ArrayHelper::getColumn($assets, 'loan_id');

                $loanQuery = Loan::find();

                if (1 === $type) {
                    $loanQuery->andWhere(['status' => 5])->orWhere(['is_jixi' => true, 'status' => ['3', '7']]); //查询还款中项目
                } elseif (3 === $type) {
                    $loanQuery->andWhere(['status' => [5, 6]]); //查询已还清项目
                }
                $loanQuery->andWhere(['id' => $loansId]);
                $loans = $loanQuery->asArray()->all();
                $loans = ArrayHelper::index($loans, 'id');
            }

            foreach ($assets as $key => $asset) {
                if (isset($loans[$asset['loan_id']])) {
                    $assetsData[$key] = $asset;
                    $assetsData[$key]['loan'] = $loans[$asset['loan_id']];
                    $assetsData[$key]['txOrderTime'] = $asset['txOrderTime'];
                    if (empty($asset['credit_order_id'])) {
                        $order = Order::findOne($asset['order_id']);
                        $assetsData[$key]['txOrderTime'] = null === $order ? null : $order->getOrderTime();
                    }
                }
            }
            if (1 === $type) {
                ArrayHelper::multisort($assetsData, function ($item) {
                    return $item['loan']['finish_date'];
                }, SORT_ASC);
            } else {
                ArrayHelper::multisort($assetsData, 'id', SORT_DESC);
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $assetsData,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        return [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalCount' => count($assetsData),
            'data' => $provider->getModels(),
        ];
    }

    /**
     * 获取收益中项目待回款总金额和已回款总金额.
     * 获取已还清项目已还清总金额.
     *
     * 1. 返回值单位为元;
     * 2. 收益中项目已回款总金额,应减去提前标记回款部分的回款总金额;
     * 3. 已还清项目已回款总金额,应加上提前标记回款部分的回款总金额;
     *
     * @param int $userId 用户ID
     * @param int $type   状态标识位: 1 收益中 3 已还清
     */
    public function actionPlanStats()
    {
        $query = $this->request->query;

        $userId = $query->getInt('user_id');
        $type = $query->getInt('type');

        if (!in_array($type, [1, 3])) {
            throw $this->ex400('参数错误');
        }

        $repaidAount = 0;
        $unpaidAount = 0;

        if ($userId > 0) {
            $p = Plan::tableName();
            $l = Loan::tableName();
            $loanStatus = 1 === $type ? 5 : 6;

            $plan = Plan::find()
                ->innerJoin($l, "$l.id = $p.online_pid")
                ->where(['uid' => $userId, "$l.status" => $loanStatus])
                ->groupBy(["$p.status"])
                ->select("$p.status, sum(benxi) as benxi")
                ->asArray()
                ->all();

            $plan = ArrayHelper::index($plan, 'status');

            if (!isset($plan[0])) {     //没有未还,设默认值0
                $plan[0]['benxi'] = 0;
            }

            if (!isset($plan[1])) {     //没有已还,设默认值0
                $plan[1]['benxi'] = 0;
            }

            if (!isset($plan[2])) {     //没有提前还款,设默认值0
                $plan[2]['benxi'] = 0;
            }

            $unpaidAount = $plan[0]['benxi'];
            $repaidAount = bcadd($plan[1]['benxi'], $plan[2]['benxi'], 2);

            $userAssets = UserAsset::findAll(['amount' => 0, 'isInvalid' => false, 'user_id' => $userId]);

            $loanIds = ArrayHelper::getColumn($userAssets, 'loan_id');
            $loans = Loan::findAll(['id' => $loanIds, 'status' => 5]);
            $loanIdArr = ArrayHelper::getColumn($loans, 'id');

            foreach ($userAssets as $userAsset) {
                if (in_array($userAsset->loan_id, $loanIdArr)) {
                    $_plan = $userAsset->plan;
                    $benxi = array_sum(ArrayHelper::getColumn($_plan, 'benxi'));
                    $repaidAount = 1 === $type ? bcsub($repaidAount, $benxi, 2) : bcadd($repaidAount, $benxi, 2);
                }
            }
        }

        return [
            'unpaidAount' => $unpaidAount,
            'repaidAount' => $repaidAount,
        ];
    }

    /**
     * 获取用户收益中可转让资产列表.
     *
     * @param int $userId    用户ID
     * @param int $offset    当前查询记录偏移量
     * @param int $limit     当前查询记录一页显示条数
     *
     * 1. 当前支持一笔债权同时转让的情况,即有多条正在处理中的挂牌记录;
     * 2. 一页默认显示10条记录;
     */
    public function actionTransferableList()
    {
        $query = $this->request->query;

        $userId = $query->getInt('user_id');
        $offset = $query->getInt('offset', 0);
        $limit = $query->getInt('limit', 10);

        $data = [];
        $totalCount = 0;    //总记录条数
        $creditAmount = 0;  //可转让金额

        if ($userId > 0) {
            $assets = UserAsset::find()
                ->where(['user_id' => $userId, 'isRepaid' => false])
                ->andWhere(['>', 'amount', 0])
                ->andWhere(['allowTransfer' => true])
                ->orderBy(['orderTime' => SORT_DESC])
                ->all();

            foreach ($assets as $key => $asset) {
                if (!$asset->canBuildCredit()) {    //不满足转让条件的,去掉
                    unset($assets[$key]);
                }
            }

            if (!empty($assets)) {
                $creditAmount = array_sum(ArrayHelper::getColumn($assets, 'maxTradableAmount'));
                $totalCount = count($assets);
                $data = array_splice($assets, $offset, $limit);
            }
        }

        return [
            'limit' => $limit,
            'offset' => $offset,
            'totalCount' => $totalCount,
            'creditAmount' => $creditAmount,
            'data' => $data,
        ];
    }


    /**
     * 获取一个资产的转让销售结果（通过指定资产查找由它发起的转让的销售后得到的新增资产列表，根据 转让 进行分组）
     * @param int $id
     * @return array
     */
    public function actionSoldRes()
    {
        $query = $this->request->query;
        $assetId = $query->getInt('asset_id');
        $notes = CreditNote::find()->where(['asset_id' => $assetId])->andWhere(['>', 'tradedAmount', 0])->orderBy(['createTime' => SORT_ASC])->all();
        $soldAssets = [];
        foreach ($notes as $note) {
            $assets = UserAsset::find()->where(['note_id' => $note->id])->orderBy(['createTime' => SORT_ASC])->all();
            if ($assets) {
                $soldAssets[$note->id] = $assets;
            }
        }
        return $soldAssets;
    }

    /**
     * 根据给定的债权订单ID列表返回订单详情
     * POST 请求
     * @param array $ids
     * 列表长度限定20个
     */
    public function actionSuccessList()
    {
        $requestData = json_decode($this->getRequest()->rawBody, true);
        $ids = $requestData['credit_order_ids'];
        if (empty($ids)) {
            return ['totalCount' => 0, 'ids' => [],'data' => []];
        }
        $ids = array_slice($ids, 0, 20);
        $assets = UserAsset::find()->where(['in', 'credit_order_id', $ids])->all();
        return ['totalCount' => count($assets), 'ids' => $ids, 'data' => $assets];
    }
}
