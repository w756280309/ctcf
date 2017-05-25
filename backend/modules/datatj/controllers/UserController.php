<?php

namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
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
        $month = date('Y-m', strtotime('-1 month'));

        $db = Yii::$app->db;
        $monthAt = strtotime($month);

        //按月统计某客户的投资年化金额
        $orderSql = <<<Order
            SELECT o.uid as user_id, SUM( TRUNCATE( (
            IF( p.refund_method >1, o.order_money * p.expires /12, o.order_money * p.expires /365 ) ) , 2 )
            ) AS annual
            FROM online_order o
            INNER JOIN online_product p ON o.online_pid = p.id
            INNER JOIN user u ON u.id = o.uid
            WHERE DATE( FROM_UNIXTIME( o.order_time ) ) >= :startTime
            AND DATE( FROM_UNIXTIME( o.order_time ) ) <= :endTime
            AND o.status =1
            AND p.is_xs =0
            GROUP BY o.uid
Order;

        $orderAnnual = $db->createCommand($orderSql, [
            'startTime' => date('Y-m-01', $monthAt),
            'endTime' => date('Y-m-t', $monthAt),
        ])->queryAll();

        $orderUids = ArrayHelper::getColumn($orderAnnual, 'user_id');
        $orderAnnual = ArrayHelper::index($orderAnnual, 'user_id');

        //按月统计某客户的实际回款年化金额
        $repaymentSql = <<<REPAYMENT
            SELECT op.uid AS user_id, SUM( TRUNCATE( (
            IF( p.refund_method >1, op.benxi * p.expires /12, op.benxi * p.expires /365 ) ) , 2 )
            ) AS annual
            FROM online_repayment_plan op
            INNER JOIN online_product p ON op.online_pid = p.id
            WHERE op.status =0
            AND op.asset_id IS NULL 
            AND op.refund_time
            BETWEEN :startAt 
            AND :endAt 
            AND p.is_xs =0
            GROUP BY op.uid
REPAYMENT;

        $repaymentAnnual = $db->createCommand($repaymentSql, [
            'startAt' => $monthAt,
            'endAt' => strtotime($month.' + 1 month') - 1,
        ])->queryAll();

        $repaymentUids = ArrayHelper::getColumn($repaymentAnnual, 'user_id');
        $repaymentAnnual = ArrayHelper::index($repaymentAnnual, 'user_id');

        //获取总的用户ID集合
        $uids = ArrayHelper::merge($orderUids, $repaymentUids);

        //查询用户信息
        $u = User::tableName();
        $users = User::find()
            ->innerJoinWith('info')
            ->where(["$u.id" => $uids])
            ->orderBy(["$u.id" => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $users,
        ]);

        //查询用户分销商信息
        $affiliators = UserAffiliation::find()
            ->innerJoinWith('affiliator')
            ->where(['user_id' => $uids])
            ->indexBy('user_id')
            ->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'repaymentAnnual' => $repaymentAnnual,
            'orderAnnual' => $orderAnnual,
            'affiliators' => $affiliators,
            'month' => $month,
        ]);
    }
}
