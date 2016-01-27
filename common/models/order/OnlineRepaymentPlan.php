<?php

namespace common\models\order;

use yii\behaviors\TimestampBehavior;
use common\models\product\OnlineProduct;
use common\lib\product\ProductProcessor;
use common\models\sms\SmsMessage;
use common\lib\bchelp\BcRound;
use Yii;

/**
 * This is the model class for table "online_repayment_plan".
 */
class OnlineRepaymentPlan extends \yii\db\ActiveRecord
{
    const STATUS_WEIHUAN = 0;//0、未还
    const STATUS_YIHUAN = 1;// 1、已还
    const STATUS_TIQIAM = 2;// 2、提前还款
    const STATUS_WUXIAO = 3;// 3，无效;

    public static function createSN($pre = 'hkjh')
    {
        $pre_val = 'HP';
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
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
        ];
    }

    public static function createPlan($pid = null)
    {
        if (empty($pid)) {
            return false;
        }
        $product = OnlineProduct::findOne($pid);
        if (empty($product)) {
            return false;
        }

        $pp = new ProductProcessor();
        $start_jixi = date('Y-m-d', $product->jixi_time);
        $days = $pp->LoanTimes($start_jixi, null, $product->finish_date, 'd', true);
        $expires = $days['days'][1]['period']['days'];
        $orders = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => OnlineOrder::STATUS_SUCCESS])->asArray()->all();
        $transaction = Yii::$app->db->beginTransaction();
        OnlineProduct::updateAll(['is_jixi' => 1], ['id' => $pid]);//修改已经计息

        $username = '';
        $sms = new SmsMessage([
            'template_id' => Yii::$app->params['sms']['manbiao'],
            'level' => SmsMessage::LEVEL_LOW,
        ]);

        foreach ($orders as $order) {
            $order['expires'] = $expires;
            $plans = self::getPlansdata($product,$order);//分期计算每一期的本金利息还款时间等
            foreach ($plans as $plan){
                $plan_model = self::initPlan($order,$plan);
                if (!$plan_model->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($username != $order['username']) {
                $message = [
                    $order['username'],
                    $product->title,
                    date('Y-m-d', $product->jixi_time),
                    Yii::$app->params['contact_tel']
                ];
                $_sms = clone $sms;
                $_sms->uid = $order['uid'];
                $_sms->mobile = $order['mobile'];
                $_sms->message = json_encode($message);
                $_sms->save();
            }
            $username = $order['username'];
        }
        $transaction->commit();
        return true;
    }

    public static function getPlansdata($product, $order) {
        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int)$product->refund_method) {//到期本息
            return self::calcRepayment($order, 'd', $product->jixi_time);
        } else if (OnlineProduct::REFUND_METHOD_MONTH === (int)$product->refund_method) {//按月还息            
            return self::calcRepayment($order, 'm', $product->jixi_time);
        } else if (OnlineProduct::REFUND_METHOD_QUARTER === (int)$product->refund_method) {//按季度还息
            return self::calcRepayment($order, 'q', $product->jixi_time);
        } else if (OnlineProduct::REFUND_METHOD_YEAR === (int)$product->refund_method) {//按年还息
            return self::calcRepayment($order, 'y', $product->jixi_time);
        }
        return false;
    }

    public static function initPlan($ord,$initplan){
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
     * 返回计划应还信息
     * @param type $order 订单信息
     * @param type $periodType d:day m:month q:quarter y:year
     * @return type
     */
    public static function calcRepayment($order, $periodType, $jixitime) {
        $pp = new ProductProcessor();
        $bc = new BcRound();
        bcscale(14);
        $total_lixi = $pp->getProductDayReturn($order['yield_rate'], $order['order_money'], $order['expires']);
        $each_day_lixi = $pp->getProductDayReturn($order['yield_rate'], $order['order_money'], 1 , FALSE);//每日利息       
        $qiday = $pp->getDays($periodType);//对应$periodType的每期的天数
        $each_lixi = bcmul($qiday, $each_day_lixi); //每期利息
        $qishu = $pp->getQishu($order['expires'], $periodType);
        $lixi_arr = array();
        for ($i = 0; $i < $qishu; $i++) {
            $cur_lixi = ($i === $qishu - 1) ? $bc->bcround(bcsub($total_lixi, bcmul($each_lixi, $i)), 2) : $bc->bcround($each_lixi, 2);
            $cur_bj = ($i === $qishu - 1) ? $order['order_money'] : 0;
            $lixi_arr[] = [
                'qishu' => ($i + 1),
                'benxi' => $bc->bcround(bcadd($cur_lixi, $cur_bj), 2),
                'benjin' => $cur_bj,
                'lixi' => $cur_lixi,
                'refund_time' => strtotime($pp->LoanTerms('d1', date('Y-m-d', $jixitime), 1 + (($i === $qishu - 1) ? $order['expires'] : ($i + 1) * $qiday))),
            ];
        }
        return $lixi_arr;
    }

}
