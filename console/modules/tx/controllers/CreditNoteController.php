<?php

namespace console\modules\tx\controllers;

use common\utils\SecurityUtils;
use common\models\tx\CreditNote;
use common\models\tx\CreditOrder;
use common\models\order\OnlineRepaymentPlan as RepaymentPlan;
use common\models\tx\SmsMessage;
use common\models\user\User;
use common\models\tx\UserAsset;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class CreditNoteController extends Controller
{
    /**
     * 定时任务-关于时间结束并且实际购买金额小于转让金额及撤销处理中的挂牌记录的撤销处理
     * 执行频率1min
     * 每次处理条数10条
     */
    public function actionCancel()
    {
        $cancelNotes = $this->notesForCancel();
        $currentTime = date('Y-m-d H:i:s');
        $db = Yii::$app->db_tx;
        foreach ($cancelNotes as $cancelNote) {

            //自动撤销时，更改相应字段
            if ($cancelNote->endTime < $currentTime && !$cancelNote->isCancelled) {
                $cancelNote->isCancelled = true;
                $cancelNote->cancelTime = $currentTime;
                $cancelNote->save(false);
            }

            $asset = $cancelNote->asset;

            //判断是否有未处理或处理中的转让订单
            if (!(CreditOrder::find()->where(['note_id' => $cancelNote->id, 'asset_id' => $asset->id])->andWhere(['in', 'status', [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER]])->count())) {
                $transaction = $db->beginTransaction();

                //没有未处理或处理中的订单，先将转让关闭
                $cancelNote->isClosed = true;
                $cancelNote->closeTime = $currentTime;
                if ($cancelNote->save(false)) {

                    //判断该资产是否有其他未关闭的转让记录
                    if (!(creditNote::find()->where(['asset_id' => $asset->id, 'isClosed' => false])->count())) {
                        $asset->isTrading = false;
                    }

                    //转让关闭后，将资产的最大可转让金额退回
                    $restAmount = bcsub($cancelNote->amount, $cancelNote->tradedAmount, 0);
                    $sql = 'UPDATE `user_asset` SET maxTradableAmount = maxTradableAmount + :restAmount, isTrading = :isTrading, updateTime = :updateTime where id = :id';
                    $affected_rows = $db->createCommand($sql, ['restAmount' => $restAmount, 'isTrading' => $asset->isTrading, 'updateTime' => $currentTime, 'id' => $asset->id])->execute();
                    if ($affected_rows > 0) {
                        $transaction->commit();
                        //如果是自动撤销，需要发短信
                        if (!$cancelNote->isManualCanceled) {
                            $user = User::findOne(['id' => $cancelNote->user_id]);
                            $this->sendSmsForNoteAutoCancel($user, $cancelNote);
                        }
                        continue;
                    }
                    $transaction->rollBack();
                }
            }
        }
    }

    /**
     * 查询时间结束并且实际购买金额小于转让金额及撤销处理中的挂牌记录
     */
    private function notesForCancel()
    {
        return CreditNote::find()->filterWhere(['<=', 'endTime', date('Y-m-d H:i:s')])
            ->orWhere(['isCancelled' => true])
            ->andWhere(['isClosed' => false])
            ->andWhere(['>', 'amount', 'tradedAmount'])
            ->limit(10)
            ->all();
    }

    /**
     * 债权自动撤销的时候发送短信.
     */
    private function sendSmsForNoteAutoCancel(User $user, CreditNote $note)
    {
        return (new SmsMessage())->initNew(
            $user,
            118098,
            [
                $note->createTime,
                bcdiv($note->amount, 100, 2),
                bcdiv($note->tradedAmount, 100, 2),
                '400-101-5151',
            ]
        )->save();
    }

    /**
     * 在指定资产上新建指定金额的转让 credit-note/add-note
     * 折让率默认为0
     * @param int $asset_id 资产ID
     * @param float $amount 新建转让的金额，已元为单位，最多两位小数;默认为0，表示全部转让
     * @param float $discountRate 折让率 单位%，3%直接存3，范围0-3，两位小数
     */
    public function actionAddNote($asset_id, $amount = 0, $discountRate = 0)
    {
        $asset_id = intval($asset_id);
        $time = time();
        $day = date('Y-m-d', $time);
        $asset = UserAsset::findOne($asset_id);
        if (is_null($asset)) {
            throw new \Exception('指定资产不存在');
        }
        if ($amount <= 0) {
            $amount = $asset->maxTradableAmount;
        } else {
            $amount = bcmul($amount, 100, 0);
        }
        if ($asset->isRepaid) {
            throw new \Exception('指定资产已完成还款');
        }
        $user = $asset->user;
        if (is_null($user)) {
            throw new \Exception('指定资产所属用户不存在');
        }
        $order = $asset->order;
        if (is_null($order)) {
            throw new \Exception('指定资产原始订单不存在');
        }
        $loan = $asset->loan;
        if (is_null($loan)) {
            throw new \Exception('指定资产所属标的不存在');
        }
        $condition = [
            'uid' => $user->id,
            'online_pid' => $loan->id,
            'order_id' => $order->id,
        ];
        if ($asset->note_id) {
            //此资产是通过购买转让得到
            $condition['asset_id'] = $asset->id;
        } else {
            //次资产是通过购买标的得到
            $condition['asset_id'] = null;
        }

        $repaymentPlans = RepaymentPlan::find()->where($condition)->all();
        if (empty($repaymentPlans)) {
            throw new \Exception('指定资产的还款计划不存在');
        }
        //转让时间必须大于标的计息时间
        if ($day <= $loan->getStartDate()) {
            throw new \Exception('转让时间必须大于标的计息时间');
        }
        //转让时间必须小于标的下一个还款日的前一天（暂定）
        foreach ($repaymentPlans as $plan) {
            //判断转让时间处于哪一期
            if ($time < $plan['refund_time']) {
                $lastRefundTime = $plan['refund_time'];
            }
        }
        if (!isset($lastRefundTime)) {
            throw new \Exception('转让时间必须小于最后一个还款日期');
        }
        //下一个还款日前一天不能转让
        if ($time > strtotime('-1 day', $lastRefundTime)) {
            throw new \Exception('下一个还款日前一天不能转让');
        }
        //转让金额必须小于资产的最大可转让金额
        if ($amount > $asset->maxTradableAmount) {
            throw new \Exception('转让金额必须小于资产的最大可转让金额');
        }

        $transaction = Yii::$app->db_tx->beginTransaction();
        try {
            $note = CreditNote::initNew($asset, $amount, $discountRate);
            if ($loan->isTest) {
                $note->isTest = true;
            }
            if (!$note->save()) {
                throw new \Exception('转让发起失败');
            }
            $sql = 'UPDATE user_asset SET `isTrading` = 1, `maxTradableAmount` =  `maxTradableAmount` - :amount WHERE id = :id';
            $res = \Yii::$app->db_tx->createCommand($sql, ['amount' => $amount, 'id' => $asset->id])->execute();
            if (!$res) {
                throw new \Exception('更改资产最大可转让金额失败');
            }
            $transaction->commit();
            echo '新建转让ID：' . $note->id . PHP_EOL;
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }
    }
}
