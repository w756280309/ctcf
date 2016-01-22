<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

class RechargeRecord extends \yii\db\ActiveRecord
{
    public $InstitutionID; //机构号码
    public $OrderNo; //订单号
    public $PaymentNo; //支付流水号
    public $Amount; //订单金额
    public $Fee; //支付服务手续费
    public $PayerID; //付款者ID
    public $PayerName; //付款者名称
    public $Usage; //资金用途
    public $Remark; //订单描述
    public $Payees; //收款人（以";"间隔）
    public $BankID; //银行ID
    public $AccountType; //账户类型

    const STATUS_NO = 0; //充值未处理
    const STATUS_YES = 1; //成功
    const STATUS_FAULT = 2; //失败
    const SETTLE_NO = 0; //结算未处理
    const SETTLE_ACCEPT = 10; //结算请求已经受理
    const SETTLE_IN = 30; //结算进行中
    const SETTLE_YES = 40; //结算已经执行（已发送转账指令）
    const SETTLE_FAULT = 50; //转账退回

    const PAY_TYPE_QUICK = 1;//快捷充值
    const PAY_TYPE_NET = 2;//网银充值
    const PAY_TYPE_OFFLINE = 3;//线下充值

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recharge_record';
    }

    public static function createSN($pre = '')
    {
        $pre_val = 'RC';
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }

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
            //[['fund','uid', 'sn','bank_id'], 'required'],
            [['fund', 'uid', 'bank_id', 'pay_type'], 'required'],
            [['account_id', 'uid', 'status'], 'integer'],
            [['fund'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9]{1,2})?$/', 'message' => '充值金额格式错误'],
            [['fund'], 'number', 'min' => 0.01, 'max' => 999999999],
            [['sn'], 'string', 'max' => 30],
            [['bank_id'], 'string', 'max' => 20],
            [['remark'], 'string', 'max' => 100],
            ['pay_type', 'default', 'value' => self::PAY_TYPE_QUICK],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '充值流水',
            'account_id' => 'Account ID',
            'uid' => 'Uid',
            'fund' => '充值金额',
            'bank_id' => '银行',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public static function getBankname($key = null)
    {
        $bank_show = Yii::$app->params['bank'];
        foreach ($bank_show as $val) {
            if ($val['number'] == $key) {
                return $val;
            }
        }

        return $bank_show;
    }

    public static function getSettlement($key = null)
    {
        $data = [
            self::STATUS_NO => '未结算',
            self::STATUS_YES => '已结算',
            self::STATUS_FAULT => '结算失败',
        ];
        if (!empty($key)) {
            return $data[$key];
        }

        return $data;
    }

    public static function getStatus($key = null)
    {
        $data = [
            self::STATUS_NO => '充值未处理',
            self::STATUS_YES => '充值成功',
            self::STATUS_FAULT => '充值失败',
        ];
        if (!empty($key)) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取支付人信息.
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}
