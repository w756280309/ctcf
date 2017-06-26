<?php

namespace console\controllers;

use common\models\growth\Retention;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\user\UserInfo;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class AddRetentionController extends Controller
{
    public function actionIndex()
    {
        $stamp = time() - 30 * 24 * 60 * 60;

        $orderUids = $this->orderSucc($stamp);   //获取30天及以内成功投资的用户ID集合.
        $repaymentUids = $this->repaymentSucc($stamp);   //获取最后一笔项目到期还款时间在30天以前的用户ID集合.
        $retentionUids = $this->retentionExist();  //获取已经被标志为流失的用户ID集合.

        $uids = array_diff($repaymentUids, $orderUids, $retentionUids);

        if (!empty($uids)) {
             $this->initRetention($uids);
        }

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 获取最后一笔项目到期还款时间在指定时间以前的用户ID集合.
     */
    private function repaymentSucc($stamp)
    {
        $repayment = OnlineRepaymentPlan::find()
            ->select("uid, MAX( refund_time ) as mr")
            ->groupBy('uid')
            ->having(['<', 'mr', $stamp])
            ->all();

        return ArrayHelper::getColumn($repayment, 'uid');
    }

    /**
     * 获得指定时刻到当前查询时刻这段时间内成功投资用户的ID集合.
     */
    private function orderSucc($stamp)
    {
        $orders = OnlineOrder::find()
            ->where([
                'status' => OnlineOrder::STATUS_SUCCESS,
            ])
            ->andWhere(['>=', 'order_time', $stamp])
            ->distinct('uid')
            ->select('uid')
            ->all();

        return ArrayHelper::getColumn($orders, 'uid');
    }

    /**
     * 获得已经存在retention记录的用户的ID集合.
     */
    private function retentionExist()
    {
        $retentions = Retention::find()
            ->where(['tactic_id' => [1, 2, 3]])
            ->distinct('user_id')
            ->select('user_id')
            ->all();

        return ArrayHelper::getColumn($retentions, 'user_id');
    }

    /**
     * 初始化retention记录.
     */
    private function initRetention(array $uids)
    {
        foreach ($uids as $uid) {
            $userInfo = UserInfo::findOne(['user_id' => $uid]);

            if (is_null($userInfo)) {
                continue;
            }

            $tacticId = null;

            if (bccomp($userInfo->averageInvestAmount, 10000) > 0) {    //平均单笔投资额＞10000
                $tacticId = 1;
            } elseif ((bccomp($userInfo->averageInvestAmount, 5000) >= 0
                    && bccomp($userInfo->averageInvestAmount, 10000) <= 0
                    && 1 === $userInfo->investCount)
                || (bccomp($userInfo->averageInvestAmount, 10000) <= 0
                    && $userInfo->investCount > 1)) {   //5000≤平均单笔投资额≤10000&投资次数=1或平均单笔投资额≤10000&投资次数＞1
                $tacticId = 2;
            } elseif (bccomp($userInfo->averageInvestAmount, 5000) < 0
                && 1=== $userInfo->investCount) {   //平均单笔投资额＜5000&投资次数=1
                $tacticId = 3;
            }

            if (null === $tacticId) {
                continue;
            }

            $retention = Retention::initNew($uid, 1, $tacticId);
            $retention->save();
        }
    }
}
