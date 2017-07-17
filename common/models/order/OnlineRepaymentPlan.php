<?php

namespace common\models\order;

use common\models\adminuser\AdminLog;
use common\models\payment\Repayment;
use common\models\product\RepaymentHelper;
use common\models\user\User;
use yii\behaviors\TimestampBehavior;
use common\models\product\OnlineProduct;
use common\lib\bchelp\BcRound;
use common\service\SmsService;
use Yii;

/**
 * 还款计划
 * @property int $qishu
 * @property float $benjin
 * @property float $lixi
 * @property int $refund_time
 * @property int $status
 * @property int $asset_id
 */
class OnlineRepaymentPlan extends \yii\db\ActiveRecord
{
    const STATUS_WEIHUAN = 0;//0、未还
    const STATUS_YIHUAN = 1;// 1、已还
    const STATUS_TIQIAM = 2;// 2、提前还款

    public static function createSN($pre = 'hkjh')
    {
        $pre_val = 'HP';
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float)$usec + (float)$sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx' . rand(1000, 9999), $usec);

        return $pre_val . str_replace('x', $sec, $date);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_repayment_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['online_pid', 'sn', 'order_id', 'qishu', 'benxi', 'benjin', 'lixi', 'yuqi_day', 'benxi_yue'], 'required'],
            [['online_pid', 'order_id', 'qishu', 'uid', 'refund_time', 'status'], 'integer'],
            [['benxi', 'benjin', 'lixi', 'overdue', 'benxi_yue'], 'number'],
            [['sn'], 'string', 'max' => 30],
            [['actualRefundTime'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'online_pid' => 'Online Pid',
            'sn' => 'Sn',
            'order_id' => 'Order ID',
            'qishu' => 'Qishu',
            'uid' => 'Uid',
            'benxi' => 'Benxi',
            'benjin' => 'Benjin',
            'lixi' => 'Lixi',
            'overdue' => 'Overdue',
            'yuqi_day' => 'Yuqi Day',
            'benxi_yue' => 'Benxi Yue',
            'refund_time' => 'Refund Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'actualRefundTime' => '实际还款时间',
        ];
    }

    public static function initPlan($ord, $initplan)
    {
        $plan = new self($initplan);
        $plan->sn = self::createSN();
        $plan->online_pid = $ord['online_pid'];
        $plan->order_id = $ord['id'];
        $plan->uid = $ord['uid'];
        $plan->status = self::STATUS_WEIHUAN;
        $plan->yuqi_day = '0';
        $plan->overdue = 0;
        $plan->benxi_yue = 0;//付息还本时候用到的字段
        return $plan;
    }

    /**
     * 获取总的利息.
     */
    public static function getTotalLixi(OnlineProduct $loan, OnlineOrder $ord)
    {
        bcscale(14);
        $bc = new BcRound();
        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int)$loan->refund_method) {   //到期本息
            return $bc->bcround(bcdiv(bcmul($ord->order_money, bcmul($loan->expires, $ord->yield_rate)), 365), 2);  //以订单里面的利率为准
        } else {
            return $bc->bcround(bcdiv(bcmul(bcmul($ord->order_money, $ord->yield_rate), $loan->expires), 12), 2);
        }
    }

    //保存 online_repayment_plan 和 repayment
    public static function saveRepayment(OnlineProduct $loan)
    {
        $orders = $loan->successOrders;
        if (empty($orders)) {
            throw new \Exception('没有找到成功订单');
        }
        $repaymentData = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($orders as $order) {
                /**
                 * @var  OnlineOrder $order
                 */
                $amountData = self::calcBenxi($order);
                if (empty($amountData)) {
                    throw new \Exception('还款数据不能为空');
                }
                foreach ($amountData as $key => $value) {
                    $term = $key + 1;
                    //判断还款计划是否存在
                    $plan = OnlineRepaymentPlan::find()
                        ->where(['order_id' => $order->id, 'uid' => $order->uid, 'qishu' => $term])
                        ->one();
                    if (!is_null($plan)) {
                        continue;
                    }
                    $amount = bcadd($value['principal'], $value['interest'], 2);
                    $planPrepareData = [
                        'qishu' => $term,
                        'benxi' => $amount,
                        'benjin' => $value['principal'],
                        'lixi' => $value['interest'],
                        'refund_time' => strtotime($value['date']),
                    ];
                    $plan = self::initPlan($order, $planPrepareData);
                    $plan->save(false);
                    $repaymentData[$term] = [
                        'amount' => isset($repayment[$term]['amount']) ? bcadd($repayment[$term]['amount'], $amount, 2) : $amount,
                        'principal' => isset($repayment[$term]['principal']) ? bcadd($repayment[$term]['principal'], $value['principal'], 2) : $value['principal'],
                        'interest' => isset($repayment[$term]['interest']) ? bcadd($repayment[$term]['interest'], $value['interest'], 2) : $value['interest'],
                        'dueDate' => $value['date'],
                    ];
                }
            }
            if (empty($repaymentData)) {
                throw new \Exception('标的还款数据不能为空');
            }
            foreach ($repaymentData as $term => $data) {
                $rep = new Repayment([
                    'loan_id' => $loan->id,
                    'term' => $term,
                    'dueDate' => $data['dueDate'],
                    'amount' => $data['amount'],
                    'principal' => $data['principal'],
                    'interest' => $data['interest'],
                ]);
                $rep->save(false);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private static function updateLoanWileConfirmInterest(OnlineProduct $loan)
    {
        $up['is_jixi'] = 1;
        if (empty($loan->finish_date)) {
            $endDate = $loan->getEndDate();
            $up['finish_date'] = strtotime($endDate);
            $loan->finish_date = $up['finish_date'];
        } else {
            //有截止日期时候，项目期限=截止日期 - 起息日期 + 1
            if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int)$loan->refund_method) {
                $expires = (new \DateTime(date('Y-m-d', $loan->finish_date)))->diff((new \DateTime(date('Y-m-d', $loan->jixi_time))))->days;
                $loan->expires = $expires;
                $up['expires'] = $expires;
            }
        }
        //记录标的日志
        $log = AdminLog::initNew(['tableName' => OnlineProduct::tableName(), 'primaryKey' => $loan->id], Yii::$app->user, $up);
        $log->save(false);
        $affectRows = OnlineProduct::updateAll($up, ['id' => $loan->id]);//修改已经计息
        if ($affectRows < 1) {
            throw new \Exception('更改标的数据失败');
        }
        return $loan;
    }

    public static function generatePlan(OnlineProduct $loan)
    {
        //获取所有订单
        $orders = $loan->successOrders;
        $templateId = Yii::$app->params['sms']['manbiao'];
        $userIds = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $loan = self::updateLoanWileConfirmInterest($loan);
            self::saveRepayment($loan);

            foreach ($orders as $order) {
                /**
                 * @var OnlineOrder $order
                 * @var User $user
                 */
                $user = $order->user;
                $message = [
                    $user->getName(),
                    $loan->title,
                    date('Y-m-d', $loan->jixi_time),
                    Yii::$app->params['platform_info.contact_tel'],
                ];
                //已经发过短信的用户不重复发送短信
                if (in_array($order->uid, $userIds)) {
                    continue;
                }
                SmsService::send($user->getMobile(), $templateId, $message, $user);
                $userIds[] = $order->uid;
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 计算每期应还本息.
     * @param \common\models\order\OnlineOrder $ord 订单对象
     *
     * 要点:
     * 1. 到期本息和自然日期计息的方式,都是按照天数计算的;
     * 2. 其他计息方式,是按照月份来计算的;
     */
    public static function calcBenxi(OnlineOrder $ord)
    {
        if (!$ord || !$ord->loan) {
            throw new \Exception();
        }
        /**
         * @var OnlineProduct $loan
         */
        $loan = $ord->loan;
        $paymentDates = $loan->getPaymentDates();
        if (empty($paymentDates)) {
            throw new \Exception('标的还款日期不能为空');
        }

        $repaymentMethod = intval($loan->refund_method);//还款方式
        $startDate = (new \DateTime())->setTimestamp($loan->jixi_time)->format('Y-m-d');//计息日期
        $duration = intval($loan->expires);//项目期限，当 $refundMethod === 1 时候，单位为天，否则单位为月
        $apr = $ord->yield_rate;//订单的实际利率
        $amount = $ord->order_money;//订单金额
        return RepaymentHelper::calcRepayment($paymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr);
    }

    public function getLoan()
    {
        return $this->hasOne(OnlineProduct::className(), ['id' => 'online_pid']);
    }
}