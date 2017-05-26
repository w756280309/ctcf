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

        $users = $userQuery->orderBy(["$u.id" => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $users,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'repaymentAnnual' => $repaymentAnnual,
            'orderAnnual' => $orderAnnual,
            'affiliators' => $affiliators,
            'affs' => $affs,
            'month' => $month,
            'affId' => $affId,
            'userType' => $userType,
        ]);
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
