<?php

namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\user\User;
use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class UserController extends BaseController
{
    /**
     * 统计新增老客复投、老客新增、新客新增数据.
     */
    public function actionIndex()
    {
        $request = Yii::$app->request->get();

        //按照月份查询
        if (isset($request['month']) && !empty($request['month'])) {
            $month = $request['month'];
        } else {
            $month = date('Y-m', strtotime('-1 month'));
        }

        $orderAnnual = $this->orderAnnual($month);
        $orderUids = [];
        $affiliators = [];
        $affId = null;
        $userType = null;

        if (isset($request['aff_id'])) {
            $affId = (int) $request['aff_id'];
        }

        if (isset($request['user_type'])) {
            $userType = (int) $request['user_type'];
        }

        if (!empty($orderAnnual)) {
            $orderUids = ArrayHelper::getColumn($orderAnnual, 'user_id');
            $orderAnnual = ArrayHelper::index($orderAnnual, 'user_id');

            //查询用户分销商信息
            $affQuery = UserAffiliation::find()
                ->innerJoinWith('affiliator')
                ->where(['user_id' => $orderUids]);

            if (null !== $affId && $affId > 0) {
                $affQuery->andWhere(['affiliator_id' => $request['aff_id']]);
            }

            $affiliators = $affQuery
                ->indexBy('user_id')
                ->all();

            //按照网点,即分销商查询
            if (null !== $affId) {
                $affUids = ArrayHelper::getColumn($affiliators, 'user_id');

                if ($affId > 0) {
                    $orderUids = array_intersect($affUids, $orderUids);
                } elseif (-1 === $affId) {
                    $orderUids = array_diff($orderUids, $affUids);
                }
            }
        }

        $repaymentAnnual = $this->repaymentAnnual($month, $orderUids);

        if (!empty($repaymentAnnual)) {
            $repaymentAnnual = ArrayHelper::index($repaymentAnnual, 'user_id');
        }

        $affs = Affiliator::find()
            ->select('id, name')
            ->indexBy('id')
            ->asArray()
            ->all();

        //查询用户信息
        $u = User::tableName();
        $userQuery = User::find()
            ->innerJoinWith('info')
            ->where(["$u.id" => $orderUids]);

        if (1 === $userType) {
            $userQuery->andWhere("DATE_FORMAT( firstInvestDate , '%Y-%m') = :month", [
                'month' => $month,
            ]);
        } elseif (2 === $userType) {
            $userQuery->andWhere("DATE_FORMAT( firstInvestDate , '%Y-%m') <> :month", [
                'month' => $month,
            ]);
        }

        $users = $userQuery
            ->orderBy(["$u.id" => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $users,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'userData' => $this->dataProcess($users, $month, $repaymentAnnual, $orderAnnual, $affiliators),
            'affs' => $affs,
            'month' => $month,
            'affId' => $affId,
            'userType' => $userType,
        ]);
    }

    /**
     * 处理数据.
     *
     * 1. 计算每个老客用户在指定月份的老客复投金额、老客新增金额;
     * 2. 计算每个新客用户在指定月份的新客新增金额;
     * 3. 根据搜索出来的数据集合,计算老客复投金额、老客新增金额、新客新增金额总计;
     */
    public function dataProcess($users, $month, $repaymentAnnual, $orderAnnual, $affiliators)
    {
        $totalNewGrowAmount = 0;
        $totalOldGrowAmount = 0;
        $totalOldRepeatAmount = 0;
        $data = [];

        foreach ($users as $user) {
            //计算新客新增金额或老客新增金额
            $oa = isset($orderAnnual[$user->id]) ? $orderAnnual[$user->id]['annual'] : 0;
            $ra = isset($repaymentAnnual[$user->id]) ? $repaymentAnnual[$user->id]['annual'] : 0;
            $amount = bcsub($oa, $ra, 2);
            $amount = $amount > 0 ? $amount : 0;

            if (substr($user->info->firstInvestDate, 0, 7) === $month) {
                $data[$user->id]['user_type'] = '新客';
                $data[$user->id]['new_grow_amount'] = $amount;
                $totalNewGrowAmount = bcadd($totalNewGrowAmount, $amount, 14);
            } else {
                $data[$user->id]['user_type'] = '老客';
                $data[$user->id]['old_grow_amount'] = $amount;
                $data[$user->id]['old_repeat_amount'] = min($oa, $ra);   //计算老客复投金额
                $totalOldGrowAmount = bcadd($totalOldGrowAmount, $amount, 14);
                $totalOldRepeatAmount = bcadd($totalOldRepeatAmount, $data[$user->id]['old_repeat_amount'], 14);
            }

            $data[$user->id]['affiliator'] = isset($affiliators[$user->id]) ? $affiliators[$user->id]->affiliator->name : '官方';
        }

        return [
            'total_new_grow_amount' => $totalNewGrowAmount,    //新客新增金额总计
            'total_old_grow_amount' => $totalOldGrowAmount,    //老客新增金额总计
            'total_old_repeat_amount' => $totalOldRepeatAmount,  //老客复投金额总计
            'data' => $data,
        ];
    }

    /**
     * 统计指定月份某客户的投资年化金额.
     */
    private function orderAnnual($month)
    {
        $orderSql = <<<Order
            SELECT o.uid as user_id, SUM( TRUNCATE( (
            IF( p.refund_method >1, o.order_money * p.expires /12, o.order_money * p.expires /365 ) ) , 2 )
            ) AS annual
            FROM online_order o
            INNER JOIN online_product p ON o.online_pid = p.id
            INNER JOIN user u ON u.id = o.uid
            WHERE DATE_FORMAT( FROM_UNIXTIME( o.order_time ) , '%Y-%m') = :month
            AND o.status =1
            AND p.is_xs =0
            GROUP BY o.uid
Order;

        $orderAnnual = Yii::$app->db->createCommand($orderSql, [
            'month' => $month,
        ])->queryAll();

        return $orderAnnual;
    }

    /**
     * 统计指定月份指定客户的实际回款年化金额.
     */
    private function repaymentAnnual($month, array $uids)
    {
        if (empty($uids)) {
            return null;
        }

        $uids = implode(',', $uids);

        $repaymentSql = <<<REPAYMENT
            SELECT op.uid AS user_id, SUM( TRUNCATE( (
            IF( p.refund_method >1, op.benxi * p.expires /12, op.benxi * p.expires /365 )), 2 )
            ) AS annual
            FROM online_repayment_plan op
            INNER JOIN online_product p ON op.online_pid = p.id
            WHERE op.status in (1, 2)
            AND op.uid in ($uids)
            AND DATE_FORMAT( op.actualRefundTime , '%Y-%m') = :month
            GROUP BY op.uid
REPAYMENT;

        $repaymentAnnual = Yii::$app->db->createCommand($repaymentSql, [
            'month' => $month,
        ])->queryAll();

        return $repaymentAnnual;
    }
}
