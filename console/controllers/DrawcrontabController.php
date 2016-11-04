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
    /**
     * 确认提现结果通知.
     *
     * 查询条件：
     * 查询状态 status => [DrawRecord::STATUS_EXAMINED, DrawRecord::STATUS_ZERO]
     * 查询间隔5min
     * 查询时间段 1周以内
     * 倒序
     *
     * 对于本地未审核申请的且创建时间>15分钟的订单查询联动状态并更新
     */
    public function actionConfirm()
    {
        $records = DrawRecord::find()
            ->where(['lastCronCheckTime' => null])
            ->orWhere(['<', 'lastCronCheckTime', time() - 5 * 60])   //查询间隔为五分钟
            ->andWhere(['in', 'status', [DrawRecord::STATUS_EXAMINED, DrawRecord::STATUS_ZERO]])
            ->andWhere(['>', 'created_at', strtotime('-1 week')])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach ($records as $record) {

            //只有未审核申请的并且当前时间超过订单时间15分钟且联动返回成功才处理成审核成功
            if ($record->status === DrawRecord::STATUS_ZERO) {
                if ((time() - $record->created_at) > 15 * 60) {
                    $drawResp = \Yii::$container->get('ump')->getDrawInfo($record);
                    if ($drawResp->isSuccessful()) {
                        if (2 === (int) $drawResp->get('tran_state')) {
                            $record = DrawManager::ackDraw($record);
                        }
                    }
                }
            } else {
                //已审核的查询联动根据状态更新其当前状态
                DrawManager::commitDraw($record);
            }

            //与充值过程保持一致：即无论成功与否，查询一次就更改一次lastCronCheckTime
            $record->lastCronCheckTime = time();
            $record->save(false);
        }
    }
}
