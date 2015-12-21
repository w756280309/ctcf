<?php

namespace common\models\order;
use yii\behaviors\TimestampBehavior;
use common\models\product\OnlineProduct;
use Yii;
use common\lib\product\ProductProcessor;

/**
 * This is the model class for table "online_repayment_plan".
 *
 */
class OnlineRepaymentPlan extends \yii\db\ActiveRecord {

    
    const STATUS_WEIHUAN=0;//0、未还 
    const STATUS_YIHUAN=1;// 1、已还 
    const STATUS_TIQIAM=2;// 2、提前还款 
    const STATUS_WUXIAO=3;// 3，无效;
    
    public static function createSN($pre = 'hkjh') {
        $pre_val = "HP";
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999), $usec);
        return $pre_val . str_replace('x', $sec, $date);
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'online_repayment_plan';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['online_pid', 'sn', 'order_id', 'qishu', 'benxi', 'benjin', 'lixi', 'yuqi_day', 'benxi_yue'], 'required'],
            [['online_pid', 'order_id', 'qishu', 'uid', 'refund_time', 'status'], 'integer'],
            [['benxi', 'benjin', 'lixi', 'overdue', 'benxi_yue'], 'number'],
            [['sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
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
    
    public static function createPlan($pid=null){
        if(empty($pid)){
            return FALSE;
        }
        $product = OnlineProduct::findOne($pid);
        if(empty($product)){
            return FALSE;
        }
        
        $plan = new OnlineRepaymentPlan();
        $pp = new ProductProcessor();
        $start_jixi = date('Y-m-d',$product->jixi_time+24*3600);
        $days = $pp->LoanTimes($start_jixi, null, $product->finish_date, 'd', true);
        $expires = $days['days'][1]['period']['days'];
        $orders = OnlineOrder::find()->where(['online_pid'=>$pid,'status'=>  OnlineOrder::STATUS_SUCCESS])->asArray()->select('id,order_money,refund_method,yield_rate,expires,uid,order_time')->all();
        OnlineProduct::updateAll(['is_jixi'=>1],['id'=>$pid]);//修改已经计息
        OnlineOrder::updateAll(['expires'=>$expires],['online_pid'=>$pid]);//修改计息天数
        
        foreach ($orders as $order){
            $plan_model = clone $plan;
            $order['expires'] = $expires;
            $processor = $pp->getProductReturn($order);
            $plan_model->sn=  self::createSN();
            $plan_model->online_pid= $pid;
            $plan_model->order_id= $order['id'];
            $plan_model->qishu= 1;//默认先是1
            $plan_model->benxi= bcadd($order['order_money'], $processor['order_return'],2);//
            $plan_model->benjin= $order['order_money'];
            $plan_model->lixi=$processor['order_return'];
            $plan_model->uid=$order['uid'];
            $plan_model->status = OnlineRepaymentPlan::STATUS_WEIHUAN;
            $plan_model->yuqi_day='0';
            $plan_model->overdue=0;
            $plan_model->benxi_yue=0;//付息还本时候用到的字段
            $plan_model->refund_time=  strtotime($pp->LoanTerms('d1', date('Y-m-d',$order['order_time']), $order['expires']));
            if(!$plan_model->save()||!$plan_model->validate()){
                return false;
            }
        }
        return true;
    }

}
