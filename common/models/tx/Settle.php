<?php

namespace common\models\tx;

use Yii;
use Zii\Model\ActiveRecord;

/**
 * 对账类文件 对账项：金额，手续费，交易日期
 *
 * @property int      $id         主键
 * @property string   $txSn       平台交易流水号，最长60
 * @property string   $fee        平台手续费，长度14，小数点后0位
 * @property string   $amount     平台对账金额，长度14，小数点后0位
 * @property smallint $txType     交易类型
 *                                1 企业网银充值
 *                                2 个人网银充值
 *                                3 个人快捷充值
 *                                4 企业账户提现
 *                                5 个人账户提现
 *                                6 托管用户开户
 * @property date     $txDate     平台交易日期
 * @property string   $fcFee      托管方手续费，长度14，小数点后0位
 * @property string   $fcAmount   托管方对账金额，长度14，小数点后0位
 * @property date     $fcDate     托管方交易日期
 * @property string   $fcSn       托管方交易流水号，最长60，当txType为6时，该字段存储用户标识
 * @property date     $settleDate 对账日期
 * @property bool     $isChecked  是否已检查
 * @property bool     $isSettled  是否对账成功
 */
class Settle extends ActiveRecord
{
    const CORP_EBANK_RECHARGE = 1; //企业网银充值
    const PERS_EBANK_RECHARGE = 2; //个人网银充值
    const PERS_QPAY_RECHARGE = 3; //个人快捷充值
    const CORP_DRAW = 4; //企业账户提现
    const PERS_DRAW = 5; //个人账户提现
    const PERS_REGISTER = 6; //托管用户开户

    public static function getDb()
    {
        return Yii::$app->db_tx;
    }

    public static function initNew()
    {
        $model = new self([
            'isChecked' => false,
            'isSettled' => false,
        ]);

        return $model;
    }
}
