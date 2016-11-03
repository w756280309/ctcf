<?php

namespace common\view;

use common\models\product\RateSteps;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;

class LoanHelper
{
    /**
     * 获取项目利率,格式为去掉小数点右端多余的0
     * @param object $loan 标的信息对象
     * @return string 当不是阶梯利率标的或阶梯利率为空时,返回标的年化收益率,否则,返回字符串形式的年化收益率~最大阶梯利率值,如10~12
     */
    public static function getDealRate(OnlineProduct $loan)
    {
        $baseRate = OnlineProduct::calcBaseRate($loan->yield_rate, $loan->jiaxi);

        if (!$loan->isFlexRate || null === $loan->rateSteps) {
            return StringUtils::amountFormat2($baseRate);
        }

        $topRate = RateSteps::getTopRate(RateSteps::parse($loan->rateSteps));
        if (false === $topRate) {
            return StringUtils::amountFormat2($baseRate);
        }

        if (null !== $loan->jiaxi) {
            $topRate = bcsub($topRate, $loan->jiaxi, 2);
        }

        return StringUtils::amountFormat2($baseRate).'～'.StringUtils::amountFormat2($topRate);
    }
}