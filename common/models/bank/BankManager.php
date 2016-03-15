<?php

namespace common\models\bank;

use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
/**
 * 银行服务类.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class BankManager
{
    /**
     * @param type $cardNum
     *
     * @throws \Exception
     */
    public static function getBankFromCardNo($cardNum)
    {
        $bindigits = BankCardBin::find()->where(['cardDigits' => strlen($cardNum)])->select('binDigits')->distinct('binDigits')->all();
        foreach ($bindigits as $bin) {
            $card_pre = substr($cardNum, 0, $bin->binDigits);
            $bin = BankCardBin::find()->where(['cardBin' => $card_pre])->one();
            if ($bin) {
                return $bin;
            }
        }
        throw new \Exception('找不到匹配信息');
    }

    /**
     * 是否是借记卡
     *
     * @param BankCardBin $bin
     */
    public static function isDebitCard(BankCardBin $bin)
    {
        if ('借记卡' !== $bin->cardType) {
            return false;
        }

        return true;
    }

    /**
     * 获取快捷卡列表.
     */
    public static function getQpayBanks()
    {
        $qpaybanks = QpayConfig::find()->where(['isDisabled' => 0])->all();

        return $qpaybanks;
    }

    /**
     * 获取网银卡信息列表
     *
     * @param type $type 'personal'  || 'business'
     *
     * @return EbankConfig
     *
     * @throws \Exception
     */
    public static function getEbank($type)
    {
        $cond['isDisabled'] = 0;
        if ('personal' === $type) {
            $cond['typePersonal'] = 1;
        } elseif ('business' === $type) {
            $cond['typeBusiness'] = 1;
        } else {
            throw new \Exception('参数不合规');
        }
        $ebanks = EbankConfig::find()->where($cond)->all();

        return $ebanks;
    }
    
    /**
     * 充值限额验证
     * @param UserBanks $banks
     * @param type $money
     * @return boolean
     * @throws \Exception
     */
    public static function verifyQpayLimit(UserBanks $banks, $money)
    {
        $config = QpayConfig::findOne($banks->bank_id);
        if (bccomp($config->singleLimit, $money) < 0) {
            throw new \Exception('超过单笔' . \Yii::$app->functions->toFormatMoney($config->singleLimit) . '限额');
        }
        $t = time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        $rc = RechargeRecord::find()->where(['status' => 1, 'pay_type' => 1])->andFilterWhere(['between','created_at',$start,$end])->sum('fund');
        if (bccomp(bcadd($rc, $money), $config->dailyLimit) > 0) {
            throw new \Exception('超过单日' .\Yii::$app->functions->toFormatMoney($config->dailyLimit) . '限额');
        }
        return true;
    }
    
}
