<?php

namespace Zii\Model;

trait CoinsTrait
{
    /**
     * 计算用户的财富值.
     *
     * @return string 保留整数位(下取整)的用户财富值.
     */
    public function getCoins()
    {
        return bcdiv($this->annualInvestment, 10000, 0);
    }
}
