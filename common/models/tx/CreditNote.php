<?php

namespace common\models\tx;

use common\models\promo\InviteRecord;
use Yii;
use Zii\Model\ActiveRecord;

/**
 * 挂牌记录
 * Class CreditNote.
 *
 * @property int        id 主键
 * @property int        asset_id         用户资产ID
 * @property int        user_id          转让者用户ID
 * @property int        loan_id          标的ID
 * @property int        order_id         原始订单ID
 * @property string     amount           转让金额
 * @property string     tradedAmount     实际被购买金额,
 * @property string     discountRate     折让率，单位%，3%直接存3，范围0-3，两位小数
 * @property bool       isClosed         结束状态
 * @property bool       isCancelled      撤销状态
 * @property string     config           配置信息，json_encode(['hold_days' => '最低持有天数', 'transfer_period' => '转让周期', 'max_discount_rate' => '最大折让率', 'trade_count_limit' => '可转让次数', 'fee_rate' => '手续费率，千分之三保存为0.003', 'min_order_amount' => '起投金额', 'incr_order_amount' => '递增金额'])
 * @property bool       isTest           是否是测试记录
 * @property string     createTime       创建时间
 * @property string     endTime          到期时间（开始时间+转让周期）
 * @property string     closeTime        关闭时间（被购买完的时间，或撤销到期后关闭）
 * @property string     cancelTime       撤销时间（手工撤销或自动撤销）
 * @property bool       isManualCanceled 手动撤销状态
 */
class CreditNote extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db_tx;
    }

    public function rules()
    {
        return [
            [['asset_id', 'user_id', 'amount', 'tradedAmount', 'discountRate', 'isClosed', 'isCancelled', 'config', 'isTest', 'createTime', 'endTime'], 'required'],
            [['amount'], 'compare', 'compareValue' => '0', 'operator' => '>'],
            [['tradedAmount', 'discountRate'], 'compare', 'compareValue' => '0', 'operator' => '>='],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_id' => '用户资产ID',
            'user_id' => '用户ID',
            'loan_id' => '标的ID',
            'order_id' => '原始订单ID',
            'amount' => '金额',
            'tradedAmount' => '实际卖出金额',
            'discountRate' => '折让率',
            'isClosed' => '债权已结束',
            'isCancelled' => '债权已取消',
            'config' => '默认配置',
            'isTest' => '测试债权',
            'createTime' => '新建时间',
            'endTime' => '结束时间',
            'closeTime' => '关闭时间',
            'cancelTime' => '撤销时间',
            'isManualCanceled' => '手动撤销',
        ];
    }

    /**
     * 初始化挂牌记录表
     *
     * @param UserAsset $asset
     * @param string    $amount       以分为单位
     * @param string    $discountRate 折让率 单位%，3%直接存3，范围0-3，两位小数
     *
     * @return CreditNote
     */
    public static function initNew(UserAsset $asset, $amount, $discountRate = '0')
    {
        $createTime = date('Y-m-d H:i:s');
        $defaultConfig = \Yii::$app->params['credit'];
        $defaultConfig['min_order_amount'] = min($asset->maxTradableAmount, $asset->loan->minOrderAmount);
        $defaultConfig['incr_order_amount'] = $asset->loan->incrOrderAmount;
        $endTime = (new \DateTime('+'.$defaultConfig['transfer_period'].'day'))->format('Y-m-d H:i:s');
        $note = new self([
            'asset_id' => $asset->id,
            'user_id' => $asset->user_id,
            'amount' => $amount,
            'discountRate' => $discountRate,
            'createTime' => $createTime,
            'endTime' => $endTime,
            'tradedAmount' => '0',
            'isClosed' => false,
            'isCancelled' => false,
            'isManualCanceled' => false,
            'config' => json_encode($defaultConfig),
            'isTest' => false,
            'loan_id' => $asset->loan_id,
            'order_id' => $asset->order_id,
        ]);

        return $note;
    }

    /**
     * 验证当前记录是否可以被发起.
     */
    public function validateCredit()
    {
        $config = json_decode($this->config, true);
        $asset = $this->asset;
        if (!$asset) {
            $this->addError('asset_id', '没有找到指定资产');

            return false;
        }
        $loan = $asset->loan;
        if (!$loan) {
            $this->addError('asset_id', '没有找到指定资产对应的标的');

            return false;
        }
        $tradeCount = intval($asset->tradeCount); //资产被转让次数
        if ($tradeCount >= $config['trade_count_limit']) {
            $this->addError('asset_id', '指定资产被转让次数超过'.$config['trade_count_limit']);
        }

        $date = date('Y-m-d', strtotime($this->createTime));
        if (!$asset->hasTransferred()) {
            //验证时间
            try {
                FinUtils::canBuildCreditByDate([
                    'startDate' => $loan->startDate,
                    'graceDays' => $loan->graceDays,
                    'repaymentDate' => $loan->repaymentDates,
                    'expires' => $loan->duration,
                    'isAmortized' => $loan->isAmortized(),
                ], [
                    'holdDays' => $config['hold_days'],
                    'duration' => $config['transfer_period'],
                    'loan_fenqi_limit' => Yii::$app->params['credit']['loan_fenqi_limit'],
                    'loan_daoqi_limit' => Yii::$app->params['credit']['loan_daoqi_limit'],
                ], $date);
            } catch (\Exception $e) {
                $this->addError('createTime', $e->getMessage());
            }
        } else {
            //该资产被转让过
            try {
                FinUtils::canBuildCreditByDate([
                    'startDate' => (new \DateTime($asset->createTime))->add(new \DateInterval('P1D'))->format('Y-m-d'),
                    'graceDays' => $loan->graceDays,
                    'repaymentDate' => $loan->repaymentDates,
                    'expires' => $loan->duration,
                    'isAmortized' => $loan->isAmortized(),
                ], [
                    'holdDays' => $config['repeatedly_hold_days'],
                    'duration' => $config['transfer_period'],
                    'loan_fenqi_limit' => Yii::$app->params['credit']['loan_fenqi_limit'],
                    'loan_daoqi_limit' => Yii::$app->params['credit']['loan_daoqi_limit'],
                ], $date);
            } catch (\Exception $e) {
                $this->addError('createTime', $e->getMessage());
            }
        }

        $order = $this->order;
        $interest = FinUtils::calculateCurrentProfit($this->loan, $this->amount, $order->apr);
        $maxRate = bcmul(bcdiv($interest, bcadd($interest, $this->amount, 14), 14), 100, 2);
        $maxDiscountRate = $config['max_discount_rate'];
        $maxDiscountRate = min($maxRate, $maxDiscountRate);
        if ($this->discountRate < 0) {
            $this->addError('discountRate', '折让率不能小于零');
        }
        if ($this->discountRate > $maxDiscountRate) {
            $this->addError('discountRate', '折让率不能大于'.$maxDiscountRate.'%');
        }

        //验证金额
        $amount = $this->amount;
        $minOrderAmount = $config['min_order_amount'];
        $incrOrderAmount = $this->loan->incrOrderAmount;
        $excessAmount = $asset->maxTradableAmount;
        try {
            FinUtils::canBuildCreditByAmount($excessAmount, $amount, $minOrderAmount, $incrOrderAmount);
        } catch (\Exception $e) {
            $this->addError('amount', $e->getMessage());
        }

        return $this->hasErrors() ? false : true;
    }

    /**
     * 计算预期剩余收益(预期收益的日期 = 截止日 - 1 - 当天)
     */
    public function getRemainingInterest()
    {
        $date = date('Y-m-d');
        $order = $this->order;
        $plans = $this->loan->getRepaymentPlan($this->amount, $order->apr);
        $totalInterest = 0;
        foreach ($plans as $plan) {
            if ($plan['date'] > $date) {
                $totalInterest = bcadd($totalInterest, $plan['interest'], 2);
            }
        }
        $currentInterest = $this->getCurrentInterest();

        return bcsub(bcmul($totalInterest, 100, 0), $currentInterest, 0);
    }

    /**
     * 计算购买方应付利息（应付利息的日期 = 当天 + 1 - 上个还款日|计息日）
     */
    public function getCurrentInterest()
    {
        $order = $this->order;
        $profit = FinUtils::calculateCurrentProfit($this->loan, $this->amount, $order->apr);

        return $profit;
    }

    /**
     * 获得用户资产信息.
     */
    public function getAsset()
    {
        return $this->hasOne(UserAsset::class, ['id' => 'asset_id']);
    }

    /**
     * 获得标的信息.
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::class, ['id' => 'loan_id']);
    }

    /**
     * 获得订单信息.
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getSuccessCreditOrders()
    {
        return $this->hasMany(CreditOrder::className(), ['note_id' => 'id'])->where(['status' => CreditOrder::STATUS_SUCCESS]);
    }

    /**
     * 获得符合条件的一批转让中的订单ID
     *
     * - 用户邀请人及其好友的转让中的订单
     * - 当前转让中最早的4笔转让
     *
     * 用处：温都可用余额转移
     *
     * @param int $userId 用户ID
     *
     * @return array
     */
    public static function getVisibleTradingIds($userId)
    {
        //获得该用户邀请的好友
        $inviteeUids = InviteRecord::find()
            ->select('invitee_id')
            ->where(['user_id' => $userId])
            ->column();

        //获得该用户的邀请人
        $userIds = InviteRecord::find()
            ->select('user_id')
            ->where(['invitee_id' => $userId])
            ->column();

        //合并用户ID
        $allUids = array_merge([$userId], $inviteeUids, $userIds);

        //转让中query
        $notesQuery = CreditNote::find()
            ->select('id')
            ->where(['isClosed' => false])
            ->andWhere(['isTest' => false]);
        $cloneQuery = Clone $notesQuery;
        $notesIds = $notesQuery->orderBy(['createTime' => SORT_ASC])
            ->andWhere(['not in', 'user_id', $allUids])
            ->limit(4)
            ->column();
        $notesExtraIds = $cloneQuery->andFilterWhere(['in', 'user_id', $allUids])
            ->column();

        return array_unique(array_merge($notesIds, $notesExtraIds));
    }
}
