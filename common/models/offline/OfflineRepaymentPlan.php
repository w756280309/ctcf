<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-15
 * Time: 上午10:55
 */
namespace common\models\offline;

use common\models\product\RepaymentHelper;
use yii\behaviors\TimestampBehavior;

/**
 * Class OfflineRepaymentPlan
 * @package common\models\offline
 */
class OfflineRepaymentPlan extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['loan_id', 'sn', 'order_id', 'qishu', 'uid', 'benxi', 'benjin', 'lixi', 'refund_time', 'operator'], 'required'],
            [['loan_id', 'order_id', 'qishu', 'uid', 'status'], 'integer'],
            [['benxi', 'benjin', 'lixi'], 'number'],
            [['sn'], 'string', 'max' => 30],
        ];
    }

    public static function tableName()
    {
        return 'offline_repayment_plan';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 计算每期应还本息.(同正式标)
     * @param $order
     */
    public static function calcBenxi(OfflineOrder $order)
    {
        $loan = OfflineLoan::findOne($order->loan_id);
        $paymentDates = $loan->getPaymentDates();   //还款日（数组）
        if (empty($paymentDates)) {
            throw new \Exception('标的还款日期不能为空');
        }
        $repaymentMethod = intval($loan->repaymentMethod);//还款方式
        $startDate = date('Y-m-d', strtotime($loan->jixi_time));//计息日期
        $duration = intval($loan->expires);//项目期限，当 $refundMethod === 1 时候，单位为天，否则单位为月
        $apr = $order->apr;//订单的实际利率
        $amount = bcmul($order->money, 10000);//订单金额(单位：万元)

        //原有计算订单的还款本息数组
        $repaymentData = RepaymentHelper::calcRepayment($paymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr);
        return $repaymentData;
    }

    public function getUser()
    {
        return OfflineUser::findOne($this->uid);
    }
    public function getOrder()
    {
        return OfflineOrder::findOne($this->order_id);
    }
    public function getLoan()
    {
        return OfflineLoan::findOne($this->loan_id);
    }
}