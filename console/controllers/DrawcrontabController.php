<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\order\OnlineFangkuan;
use common\models\user\DrawRecord;
use common\models\user\User;
use Ding\DingNotify;
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
     * 查询时间段 1周以内
     * 如果是初始状态的订单还必须满足当前时间超过订单创建时间15min
     * 状态倒序，上次查询时间倒序
     */
    public function actionConfirm()
    {
        $currentAt = time();
        $quarterAgo = $currentAt - 15 * 60;

        $records = DrawRecord::find()
            ->andWhere(['in', 'status', [DrawRecord::STATUS_ZERO, DrawRecord::STATUS_EXAMINED]])
            ->andWhere(['case status when ' . DrawRecord::STATUS_ZERO . ' then (created_at < ' . $quarterAgo . ') when '. DrawRecord::STATUS_EXAMINED .' then (created_at < ' . $currentAt . ') end' => true])
            ->andWhere(['>', 'created_at', strtotime('-1 week')])
            ->orderBy(['lastCronCheckTime' => SORT_ASC])
            ->limit(3)
            ->all();

        foreach ($records as $record) {
            $record->lastCronCheckTime = time();
            $record->save(false);

            //只有未审核的并且当前时间超过订单时间15分钟且联动返回成功才处理成审核成功
            if ($record->status === DrawRecord::STATUS_ZERO) {
                $drawResp = \Yii::$container->get('ump')->getDrawInfo($record);
                if ($drawResp->isSuccessful()) {
                    $tranState = intval($drawResp->get('tran_state'));
                    //12已冻结、13待解冻、14财务已审核, 2提现成功都可以认为成已受理
                    if (in_array($tranState, [12, 13, 14, 2])) {
                        $record = DrawManager::ackDraw($record);
                    } elseif ($tranState === 3 || $tranState === 5 || $tranState === 15)  {
                        $user = User::findOne($record->uid);
                        if (!empty($user)) {
                            (new DingNotify('wdjf'))->sendToUsers('用户[' . $user->id . ']，于' . date('Y-m-d H:i:s', $record->created_at) . ' 进行提现操作，sn '.$record->sn.'，提现失败，联动提现失败，联动返回状态:' . $drawResp->get('tran_state'));
                        }
                        $record->status = DrawRecord::STATUS_FAIL;
                        $record->save(false);
                    }
                }
            } else {
                //已审核的查询联动根据状态更新其当前状态
                DrawManager::commitDraw($record);
            }

            $this->updateFkStatus($record);   //修改放款记录的状态,成功或失败
        }
    }

    /**
     * 如果是融资会员提现,需要根据对应的提现状态,修改对应的放款记录状态.
     */
    private function updateFkStatus(DrawRecord $draw)
    {
        switch ($draw->status) {
            case DrawRecord::STATUS_SUCCESS:
                $status = OnlineFangkuan::STATUS_TIXIAN_SUCC;
                break;
            case DrawRecord::STATUS_FAIL:
            case DrawRecord::STATUS_DENY:
                $status = OnlineFangkuan::STATUS_TIXIAN_FAIL;
                break;
            default:
                $status = null;
        }

        if (null !== $status && User::USER_TYPE_ORG === $draw->user->type) {
            $fk = OnlineFangkuan::findOne(['sn' => $draw->orderSn]);

            if (null !== $fk) {
                $fk->status = $status;
                $fk->save(false);
            }
        }
    }
}
