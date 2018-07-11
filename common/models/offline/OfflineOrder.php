<?php

namespace common\models\offline;

use common\models\product\RepaymentHelper;
use yii\db\ActiveRecord;
use common\models\affiliation\Affiliator;
use Zii\Validator\CnIdCardValidator;
use Yii;

/**
 * This is the model class for table "offline_order".
 *
 * @property integer $id
 * @property integer $affiliator_id 分销商ID
 * @property integer $user_id       用户ID
 * @property integer $loan_id       线下产品ID
 * @property string  $realName      姓名
 * @property string  $mobile        联系电话
 * @property string  $money         购买金额
 * @property string  $orderDate     认购日期
 * @property string  $created_at    创建时间
 * @property string  $isDeleted     是否删除
 * @property string  $idCard        身份证号
 * @property string  $accBankName   开户行名称
 * @property string  $bankCardNo    银行卡号
 * @property string  $valueDate     起息日
 * @property string  $apr           利率
 */
class OfflineOrder extends ActiveRecord
{
    public $realName;
    public function scenarios()
    {
        return [
            'confirm' => ['valueDate'],
            'edit' => ['realName', 'accBankName', 'bankCardNo', 'apr'],
            'default' => ['affiliator_id', 'loan_id', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo', 'apr', 'valueDate'],
            'is_jixi' => ['accBankName', 'bankCardNo'], //确认计息后只可以修改银行卡信息
        ];
    }

    public function rules()
    {
        return [
            [['affiliator_id', 'loan_id', 'mobile', 'money', 'orderDate', 'created_at', 'user_id', 'idCard', 'accBankName', 'bankCardNo', 'apr'], 'required'],
            [['user_id', 'affiliator_id', 'loan_id', 'created_at'], 'integer'],
            [['realName', 'accBankName', 'bankCardNo','mobile'], 'required', 'on' => 'edit'],
            [['idCard'], 'string', 'max' => 30],
            [['bankCardNo'], 'string', 'min' => 16, 'max' => 19],
            [['idCard'], CnIdCardValidator::className()],
            ['money', 'number'],
            [['orderDate', 'valueDate'], 'safe'],
            ['apr', 'number', 'min' => '0.0001', 'max' => '1'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'affiliator_id' => '分销商ID',
            'user_id' => '用户ID',
            'loan_id' => '线下产品ID',
            'realName' => '姓名',
            'mobile' => '联系电话',
            'money' => '购买金额',//以万元为单位
            'orderDate' => '订单日期',
            'created_at' => '创建时间',
            'isDeleted' => '是否删除',
            'idCard' => '身份证号',
            'accBankName' => '开户行名称',
            'bankCardNo' => '银行卡号',
            'valueDate' => '起息日',
            'apr' => '利率',
        ];
    }

    public function getAffliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }

    public function getLoan()
    {
        return $this->hasOne(OfflineLoan::className(), ['id' => 'loan_id']);
    }

    public function getOrder_money()
    {
        return $this->money;
    }

    /**
     * 获取认购日期
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function getUser()
    {
        return $this->hasOne(OfflineUser::className(), ['id' => 'user_id']);
    }

    public function getOnlineUser()
    {
        $user = null;
        if (null !== ($user = $this->user)) {
            $user = $user->onlineUser;
        }

        return $user;
    }

    /**
     * 根据订单计算年化投资金额
     * 还款方式为等额本息时与非等额本息计算方法不同
     */
    public function getAnnualInvestment()
    {
        if ((int) $this->loan->repaymentMethod === 10) {
            $startDate = (new \DateTime($this->orderDate))->add(new \DateInterval('P1D'))->format('Y-m-d');
            $duration = intval($this->loan->expires);
            $apr = $this->apr;
            $amount = bcmul($this->money, 10000);

            return RepaymentHelper::calcDebxAnnualInvest($startDate, $duration, $apr, $amount);
        }
        if (strpos($this->loan->unit, '天') !== false) {
            $base = 365;
        } else {
            $base = 12;
        }

        return bcdiv(bcmul($this->money * 10000, $this->loan->expires, 14), $base, 2);
    }
    //分期还款最后一期
    public function getLastTerm()
    {
        return OfflineRepaymentPlan::find()->where(['order_id' => $this->id])->count();
    }
    //预期收益
    public function getExpectedEarn()
    {
        return OfflineRepaymentPlan::find()->where(['order_id' => $this->id])->sum('lixi');
    }

    public function getOrder_time()
    {
        return strtotime($this->orderDate);
    }

    public function getUserMobile()
    {
        $user = OfflineUser::findOne($this->user_id);
        if (!is_null($user)) {
            return $user->mobile;
        }
        return null;
    }

    /**
     * 判断订单状态
     * 1 => '收益中'，2 => '募集中', 3 => '已还清'
     */
    public function getRepaymentStatus()
    {
        if (!$this->loan->is_jixi) {
            return 2;
        } else {
            //获取最后一期还款计划
            $plan = OfflineRepaymentPlan::findOne([
                'uid' => $this->user_id,
                'loan_id' => $this->loan_id,
                'order_id' => $this->id,
                'qishu' => $this->getLastTerm(),
            ]);
            return $plan->status ? 3 : 1;
        }
    }

    /**
     * 获取等额本息线下所有的投标记录，$startDate和$endDate为空时获取所有线下投标记录，均不为空时获取某段时间内线下投标记录
     * @param bool $isJixi 为true时获取计息后未到项目截止时间的投标记录，为false时所有投标记录
     * @param string [optional] $startDate 开始时间
     * @param string [optional] $endDate 结束时间
     * @return array 线下投标记录数组
     */
    public static function getDebxOfflineOrdersArray($isJixi = false, $startDate = null, $endDate = null)
    {
        $where = null;
        if ($isJixi) {
            $date = date('Y-m-d H:i:s');
            $where = " AND l.is_jixi = 1 
                AND '" . $date . "' <= l.finish_date";
        }
        if ($startDate === null && $endDate === null) {
            $sql = "SELECT o.orderDate, l.expires, (o.money * 10000) as money, o.apr 
                FROM offline_order o 
                INNER JOIN offline_loan l 
                ON o.loan_id = l.id 
                AND o.isDeleted = 0 
                AND l.repaymentMethod = 10" . $where;

            return Yii::$app->db->createCommand($sql)->queryAll();
        } elseif ($startDate !== null && $endDate !== null) {
            $sql = "SELECT o.orderDate, l.expires, (o.money * 10000) as money, o.apr 
            FROM offline_order o 
            INNER JOIN offline_loan l 
            ON o.loan_id = l.id 
            AND o.isDeleted = 0 
            AND l.repaymentMethod = 10 
            AND date(from_unixtime(o.created_at)) >= :startDate 
            AND date(from_unixtime(o.created_at)) <= :endDate" . $where;

            return Yii::$app->db->createCommand($sql, [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->queryAll();
        }
    }

    /**
     * 获取某个人某段时间内所有等额本息的投标记录
     * @param $userId  登录线下用户id
     * @param $startDate  开始日期
     * @param $endDate  结束日期
     * @return array  线下投标记录数组
     */
    public static function getDebxUserOfflineOrdersArray($userId, $startDate, $endDate)
    {
        $sql = "SELECT o.orderDate, l.expires, (o.money * 10000) as money, o.apr 
            FROM offline_order o
            INNER JOIN offline_loan l 
            ON o.loan_id = l.id
            INNER JOIN offline_user u 
            ON u.id = o.user_id
            WHERE o.onlineUserId = :userId 
            AND l.isDeleted = 0 
            AND l.repaymentMethod = 10 
            AND date(from_unixtime(o.created_at)) >= :startDate
            AND date(from_unixtime(o.created_at)) <= :endDate";

        return Yii::$app->db->createCommand($sql, [
            'userId' => $userId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();
    }

    /**
     * 某段时间内非等额本息标的线下投标记录
     * @param $startDate 开始时间
     * @param $endDate 结束时间
     * @return array  线下投标记录数组
     */
    public static function getOfflineOrdersArray($startDate, $endDate)
    {
        $offlineSql = "SELECT date_format(date(o.orderDate), '%Y-%m') as orderDate,
        sum(truncate((if(l.repaymentMethod > 1, o.money*l.expires*10000/12, o.money*l.expires*10000/365)), 2)) as annual 
        FROM offline_order o 
        INNER JOIN offline_loan l 
        ON o.loan_id = l.id 
        WHERE o.isDeleted = 0 
        AND l.repaymentMethod != 10 
        AND date(o.orderDate) >= :startDate 
        AND date(o.orderDate) <= :endDate 
        GROUP BY month(date(o.orderDate))";
        return Yii::$app->db->createCommand($offlineSql, [
            'startDate' => $startDate,
            'endDate' =>$endDate,
        ])->queryAll();
    }
}
