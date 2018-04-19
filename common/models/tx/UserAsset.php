<?php

namespace common\models\tx;

use common\models\order\OnlineRepaymentPlan;
use common\models\user\User;
use Yii;
use Zii\Behavior\DateTimeBehavior;
use Zii\Model\ActiveRecord;

/**
 * @property int      $id                   主键
 * @property int      $user_id              用户ID
 * @property int      $loan_id              标的ID
 * @property int      $asset_id             资产ID（当购买债权时记录被购买资产的ID）
 * @property int      $note_id              挂牌记录ID(当购买债权时记录对应的挂牌记录ID)
 * @property int      $order_id             订单ID
 * @property int      $credit_order_id      债权订单ID
 * @property bool     $isRepaid             是否已还款
 * @property string   $amount               金额
 * @property string   $orderTime            订单创建时间
 * @property string   $createTime           创建时间
 * @property string   $updateTime           更新时间
 * @property int      $tradeCount           记录资产被转让次数（当购买债权时记录该资产被购买次数）
 * @property string   $maxTradableAmount    最大可转让金额
 * @property bool     $isTrading            是否正在转让
 * @property bool     $isInvalid            是否失效(当用户资产为零且无还款计划时,标记该用户资产失效)
 * @property bool     $allowTransfer        是否允许转让(不能转让的标的生成的资产都不能转让)
 */
class UserAsset extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db_tx;
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'loan_id', 'order_id', 'amount', 'orderTime', 'allowTransfer'], 'required'],
        ];
    }

    //补充用户属性，增加预期收益和应付利息
    public function getAttributes($names = null, $except = [])
    {
        $attributes = parent::getAttributes($names, $except);
        $arr = [
            'currentInterest' => $this->getCurrentInterest(),
            'remainingInterest' => $this->getRemainingInterest(),
        ];

        return array_merge($attributes, $arr);
    }

    //计算预期收益(预期收益的日期 = 截止日 - 1 - 当天)
    public function getRemainingInterest($startTime = null)
    {
        $date = isset($startTime) ? date('Y-m-d', strtotime($startTime)): date('Y-m-d');
        $order = $this->order;
        $plans = $this->loan->getRepaymentPlan($this->maxTradableAmount, $order->apr);
        $totalInterest = 0;
        foreach ($plans as $plan) {
            if ($plan['date'] > $date) {
                $totalInterest = bcadd($totalInterest, $plan['interest'], 2);
            }
        }
        $date = isset($startTime) ? $date : null;
        $currentInterest = $this->getCurrentInterest($date);

        return bcsub(bcmul($totalInterest, 100, 0), $currentInterest, 0);
    }

    //计算应付利息（应付利息的日期 = 当天 + 1 - 上个还款日|计息日）
    public function getCurrentInterest($startTime = null)
    {
        $order = $this->order;
        $profit = FinUtils::calculateCurrentProfit($this->loan, $this->maxTradableAmount, $order->apr, $startTime);

        return $profit;
    }

    /**
     * 初始化用户债券记录.
     */
    public static function initNew()
    {
        $model = new self([
            'isRepaid' => false,
            'isTrading' => false,
            'isInvalid' => false,
            'allowTransfer' => true,
        ]);

        return $model;
    }

    /**
     * 获取用户资产对应的标的信息.
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::class, ['id' => 'loan_id']);
    }

    /**
     * 获取用户资产对应的订单信息.
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * 获取用户资产对应的订单信息.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * 获取用户资产对应的还款计划信息.
     */
    public function getPlan()
    {
        $cond = [
            'online_pid' => 'loan_id',
            'uid' => 'user_id',
        ];

        if ($this->note_id !== null) {
            $cond['asset_id'] = 'id';
            $_cond = [];
        } else {
            $cond['order_id'] = 'order_id';
            $_cond = ['asset_id' => null];
        }

        return $this->hasMany(OnlineRepaymentPlan::class, $cond)->where($_cond);
    }

    //判断当前资产是否被转让过
    public function hasTransferred()
    {
        return intval($this->tradeCount) > 0;
    }

    /**
     * 判断当前资产是否可以新建债权(判断资产的可转让金额、当前时间、转让次数)
     */
    public function canBuildCredit()
    {
        $config = \Yii::$app->params['credit'];
        $date = date('Y-m-d');
        $loan = $this->loan;
        if (empty($loan)) {
            return false;
        }
        if (!$this->allowTransfer || !$loan->allowTransfer) {
            return false;
        }

        if (!$loan) {
            return false;
        }
        if (5 !== $loan->status) {  //判断标的是否为收益中
            return false;
        }

        if ($date > $loan->endDate) {
            //标的过期
            return false;
        }

        $tradeCount = intval($this->tradeCount);//资产被转让次数
        if ($tradeCount >= $config['trade_count_limit']) {
            return false;
        }
        //验证时间
        if (!$this->hasTransferred()) {
           //该资产没有被转让过
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
                    'loan_fenqi_limit' => $config['loan_fenqi_limit'],
                    'loan_daoqi_limit' => $config['loan_daoqi_limit'],
                ], $date);
            } catch (\Exception $e) {
                return false;
            }
        } else {
            //该资产被转让过
            try {
                FinUtils::canBuildCreditByDate([
                    'startDate' => (new \DateTime($this->createTime))->add(new \DateInterval('P1D'))->format('Y-m-d'),
                    'graceDays' => $loan->graceDays,
                    'repaymentDate' => $loan->repaymentDates,
                    'expires' => $loan->duration,
                    'isAmortized' => $loan->isAmortized(),
                ], [
                    'holdDays' => $config['repeatedly_hold_days'],
                    'duration' => $config['transfer_period'],
                    'loan_fenqi_limit' => $config['loan_fenqi_limit'],
                    'loan_daoqi_limit' => $config['loan_daoqi_limit'],
                ], $date);
            } catch (\Exception $e) {
                return false;
            }
        }

        //验证金额
        $amount = $this->maxTradableAmount;
        $minOrderAmount = $this->loan->minOrderAmount;
        $incrOrderAmount = $this->loan->incrOrderAmount;
        $excessAmount = $this->maxTradableAmount;
        try {
            FinUtils::canBuildCreditByAmount($excessAmount, $amount, $minOrderAmount, $incrOrderAmount);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
