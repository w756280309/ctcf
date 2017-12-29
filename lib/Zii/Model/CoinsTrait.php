<?php

namespace Zii\Model;

use common\models\user\User;

trait CoinsTrait
{
    /**
     * 计算用户的财富值.
     *
     * @return string 保留整数位(下取整)的用户财富值.
     */
    public function getCoins()
    {
        $amount = $this->annualInvestment;
        if ($this Instanceof User && $this->offline) {
            $amount = bcadd($this->offline->annualInvestment, $this->annualInvestment, 2);
        }
        return bcdiv($amount, 10000, 0);
    }
}
