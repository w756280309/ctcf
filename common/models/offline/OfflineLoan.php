<?php

namespace common\models\offline;

use common\lib\product\ProductProcessor;
use common\models\product\RepaymentHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offline_loan".
 *
 * @property integer $id
 * @property string  $title   标的名称
 * @property string  $expires 项目期限
 * @property string  $unit    期限单位:天/月
 * @property int     $repaymentMethod   还款方式
 * @property string  $jixi_time 计息日
 */
class OfflineLoan extends ActiveRecord
{
    public function scenarios()
    {
        return [
            'confirm' => ['jixi_time'],
            'default' => ['sn', 'title',  'expires', 'unit', 'jixi_time', 'yield_rate', 'repaymentMethod', 'paymentDay'],
            'edit' => ['sn', 'title',  'expires', 'unit', 'jixi_time', 'yield_rate', 'repaymentMethod', 'paymentDay'],
            'addexcel' => ['title',  'expires', 'unit', 'jixi_time', 'yield_rate'],
        ];
    }

    public function rules()
    {
        return [
            ['sn','unique','message'=>'编号已占用'],
            ['sn', 'string', 'max' => 32],
            [['sn', 'title', 'expires', 'unit' ,'yield_rate', 'repaymentMethod'], 'required'],
            ['jixi_time','required', 'on' => ['confirm', 'edit']],
            ['title', 'string', 'max' => 255],
            ['expires','number'],
            ['finish_date', 'string', 'max' => 255],
            ['yield_rate', 'string', 'max' => 255],
            ['jixi_time', 'string', 'max' => 255],
            ['unit', 'string', 'max' => 20],
            ['repaymentMethod', 'integer'],
            ['paymentDay', 'integer', 'max' => 30],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '产品名称',
            'expires' => '产品期限',
            'unit' => '期限单位',
            'sn' => '标的序号',
            'yield_rate' => '利率',
            'jixi_time' => '起息日',
            'finish_date' => '到期日',
            'repaymentMethod' => '还款方式',
            'paymentDay' => '固定还款日',
        ];
    }

    public function getOrder()
    {
        return $this->hasMany(OfflineOrder::className(), ['loan_id' => 'id']);
    }

    //判断是否分期
    public function isAmortized()
    {
        return OfflineRepayment::find()->where(['loan_id' => $this->id])->count();
    }

    public function getRepayment()
    {
        return $this->hasMany(OfflineRepayment::className(),['loan_id' => 'id']);
    }
    //所有成交的订单
    public function getSuccessOrder()
    {
        return OfflineOrder::find()->where(['loan_id' => $this->id, 'isDeleted' => false])->all();
    }
    //获取计息日
    public function getStartDate()
    {
        return date('Y-m-d', strtotime($this->jixi_time));
    }
    //获取标的截止日
    public function getEndDate()
    {
        if (empty($this->finish_date)) {
            $pp = new ProductProcessor();
            if ($this->unit == '天') {
                $endDate = $pp->LoanTerms('d1',date('Y-m-d', strtotime($this->jixi_time)), $this->expires);
            } else if ($this->unit == '个月') {
                $endDate = $pp->LoanTerms('m1',date('Y-m-d', strtotime($this->jixi_time)), $this->expires);
            }
        } else {
            $endDate = date('Y-m-d', strtotime($this->finish_date));
        }
        return $endDate;
    }
    /**
     * 获取指定标的的所有还款日
     */
    public function getPaymentDates()
    {
        return RepaymentHelper::calcRepaymentDate(
            $this->getStartDate(),  //起息日
            $this->getEndDate(),    //结束日（最后一期还款时间）
            $this->repaymentMethod, //还款方式
            $this->expires,     //项目期限
            $this->paymentDay,  //固定还款日
            null
        );
    }

    //应还款人数
    public function getRepaymentNumber()
    {
        $count = OfflineRepaymentPlan::find()->select(['uid'])->where(['loan_id' => $this->id])->groupBy('uid')->count();
        return $count;
    }
    //应还款本金
    public function getBenjin()
    {
        return OfflineRepayment::find()->where(['loan_id' =>$this->id])->sum('principal');
    }
    //应还利息
    public function getLixi()
    {
        return OfflineRepayment::find()->where(['loan_id' =>$this->id])->sum('interest');
    }
    //应还款本息
    public function getAmount()
    {
        return OfflineRepayment::find()->where(['loan_id' =>$this->id])->sum('amount');
    }
    public function getRepayments()
    {
        return OfflineRepayment::find()->where(['loan_id' =>$this->id])->all();
    }
    //贴息，最后一期还款计划
    public function getTiexi()
    {
        return OfflineRepaymentPlan::find()->where(['loan_id' => $this->id])->sum('tiexi');
    }
}
