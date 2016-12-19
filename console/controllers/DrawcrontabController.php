<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use yii\console\Controller;

/**
 * 提现结果通知.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class DrawcrontabController extends Controller
{

    //查询次数
    private $checkCount = 20;

    /**
     * 确认提现结果通知.
     *
     * 查询条件：
     * 下次查询时间小于当前时间
     * 查询次数小于20次
     * 查询状态 status => [DrawRecord::STATUS_EXAMINED, DrawRecord::STATUS_ZERO]
     * 查询时间段 1周以内
     * 如果是初始状态的订单还必须满足当前时间超过订单创建时间15min
     * 状态倒序，id倒序
     */
    public function actionConfirm()
    {
        $currentAt = time();
        $quarterAgo = $currentAt - 15 * 60;

        $records = DrawRecord::find()
            ->where(['nextCronCheckTime' => null])
            ->orWhere(['<', 'nextCronCheckTime', time()])
            ->andWhere(['<', 'checkCount', $this->checkCount])
            ->andWhere(['in', 'status', [DrawRecord::STATUS_ZERO, DrawRecord::STATUS_EXAMINED]])
            ->andWhere(['case status when ' . DrawRecord::STATUS_ZERO . ' then (created_at < ' . $quarterAgo . ') when '. DrawRecord::STATUS_EXAMINED .' then (created_at < ' . $currentAt . ') end' => true])
            ->andWhere(['>', 'created_at', strtotime('-1 week')])
            ->orderBy(['status' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        foreach ($records as $record) {

            //只有未审核的并且当前时间超过订单时间15分钟且联动返回成功才处理成审核成功
            if ($record->status === DrawRecord::STATUS_ZERO) {
                $drawResp = \Yii::$container->get('ump')->getDrawInfo($record);

                if ($drawResp->isSuccessful()) {
                    //12已冻结、13待解冻、14财务已审核, 2提现成功都可以认为成已受理
                    if (in_array(intval($drawResp->get('tran_state')), [12, 13, 14, 2])) {
                        $record = DrawManager::ackDraw($record);
                    }
                } elseif ($drawResp->get('ret_code') === '00131013') {
                    //ret_msg = 本次订单查询操作未明，请稍后再次查询或联系运营人员; 认为联动不存在该订单
                    $record->checkCount = $this->checkCount;
                    $record->save(false);
                    continue;
                }
            } else {
                //已审核的查询联动根据状态更新其当前状态
                DrawManager::commitDraw($record);
            }

            //与充值过程保持一致：即无论成功与否，查询一次就更改一次lastCronCheckTime
            $createdDate = date('Ymd', $record->created_at);
            $lastCheckDate = date('Ymd', $record->lastCronCheckTime);
            $nextCheckTime = $record->lastCronCheckTime;
            //如果当前下一次查询日期大于订单创建日期。每次时间间隔为一天
            if (date('Ymd', $record->nextCronCheckTime) > $createdDate) {
                $record->nextCronCheckTime = $nextCheckTime + 24 * 60 * 60;
            } else {
                $record->nextCronCheckTime = $this->getNextCheckTime(null === $record->lastCronCheckTime ? $currentAt : $record->lastCronCheckTime, $record->checkCount);
                //判断下次查询日期正好为创建日期的下一天，且上次查询日期不超过订单创建日期
                //将下次查询时间定为第二日下午的4点（以往订单最晚为15:45分）
                if (date('Ymd', $record->nextCronCheckTime) > $createdDate && $lastCheckDate <= $createdDate) {
                    $record->nextCronCheckTime = strtotime(date('Y-m-d', $record->nextCronCheckTime).' 16:00:00');
                }
            }
            $record->lastCronCheckTime = time();
            $record->checkCount += 1;
            $record->save(false);
        }
    }

    /**
     * 获得下次查询时间
     *
     * @param  int $lastCheckTime
     * @param  int $count
     * @return int
     */
    private function getNextCheckTime($lastCheckTime, $count)
    {
        return (int) $lastCheckTime + 5 * pow(2, $count) * 60;
    }
}
