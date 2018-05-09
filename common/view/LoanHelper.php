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

        //todo - 临时代码，对于可主动赎回的标的利率暂定写死为9.5，因为当前库中未记录此字段
        if ($loan->isRedeemable) {
            $topRate = 9.5;
        }

        if (null !== $loan->jiaxi) {
            $topRate = bcsub($topRate, $loan->jiaxi, 2);
        }

        return StringUtils::amountFormat2($baseRate).'～'.StringUtils::amountFormat2($topRate);
    }

    /**
     * 获取标的宽限期描述信息
     * @param OnlineProduct $loan
     * @return string
     */
    public static function getGraceDaysDescription(OnlineProduct $loan)
    {
        if ($loan->kuanxianqi > 0
            && $loan->refund_method === OnlineProduct::REFUND_METHOD_DAOQIBENXI
        ) {
            $duration = $loan->getDuration();
            $expires = $duration['value'];
            $plat = \Yii::$app->params['plat_code'];
            if ($loan->kuanxianqi > $expires * 0.5) {
                return $plat == 'CTCF'
                    ? '融资方可提前还款，客户利息按实际出借期限计算。'
                    : '融资方可提前还款，客户收益按实际天数计息。';
            } else {
                return $plat == 'CTCF'
                ? '融资方可提前还款，客户利息按实际出借期限计算。'
                : '融资方可提前' . $loan->kuanxianqi . '天内任一天还款，客户收益按实际天数计息。';
            }
        }

        return '';
    }
}