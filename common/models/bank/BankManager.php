<?php

namespace common\models\bank;

/**
 * 银行服务类
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class BankManager
{
    
    /**
     * 
     * @param type $cardNum
     * @throws \Exception
     */
    public static function getBankFromCardNo($cardNum){
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
     * 获取快捷卡列表
     */
    public static function getQpayBanks() {
        $qpaybanks = ConfigQpay::find()->where(['isDisabled' => 0])->all();
        return $qpaybanks;
    }
   
    /**
     * 
     * @param type $type  'personal'  || 'personal'
     * @return ConfigEbank
     * @throws \Exception
     */
    public static function getEbank($type) {
        $cond['isDisabled'] = 0;
        if ('personal' === $type) {
            $cond['typePersonal'] = 1;
        } else if ('personal' === $type) {
            $cond['typeBusiness'] = 1;
        } else {
            throw new \Exception('参数不合规');
        }
        $ebanks = ConfigEbank::find()->where($cond)->all();
        return $ebanks;
    }
    
}
