<?php

namespace common\models\tx;

use Yii;
use Zii\Model\ActiveRecord;

/**
 * @property int      $id         主键
 * @property string   $sn         流水号，最大长度20
 * @property int      $userId     用户id
 * @property string   $amount     金额，最大长度14，小数点后0位
 * @property smallint $payType    充值方式，1快捷充值2网银充值
 * @property smallint $status     充值状态，0充值未处理1充值成功2充值失败
 * @property string   $createTime 创建时间
 */
class RechargeRecord extends ActiveRecord
{
    const STATUS_NO = 0; //充值未处理
    const STATUS_YES = 1; //成功
    const STATUS_FAULT = 2; //失败
    const SETTLE_NO = 0; //结算未处理
    const SETTLE_ACCEPT = 10; //结算请求已经受理
    const SETTLE_IN = 30; //结算进行中
    const SETTLE_YES = 40; //结算已经执行（已发送转账指令）
    const SETTLE_FAULT = 50; //转账退回

    public static function getDb()
    {
        return Yii::$app->db;
    }

    public function getAmount()
    {
        return (string) ($this->fund * 100);
    }

    public function getFee($txType)
    {
        if ($txType === Settle::PERS_QPAY_RECHARGE) {
            $calcFee = $this->amount * 0.12 / 100;
            $fee = $calcFee > 200 ? floor(round($calcFee)) : 200;
        } elseif ($txType === Settle::PERS_EBANK_RECHARGE) {
            $fee = floor(round($this->amount * 0.18 / 100));
        } elseif ($txType === Settle::CORP_EBANK_RECHARGE) {
            $fee = 1000;
        }

        return (string) $fee;
    }

    public function getUser_id()
    {
        return $this->uid;
    }

    public function getStartDate()
    {
        return date('Y-m-d', $this->created_at);
    }

    //-------以上方法用于该表未迁移到交易系统时的方法（以后会删除）--------
}
