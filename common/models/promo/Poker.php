<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

/**
 * 周周乐、扑克牌活动 - 开奖
 */
class Poker extends ActiveRecord
{
    public static function calcTerm($timeAt)
    {
        //周一当天十点之前
        if ('1' === date('w', $timeAt) && date('H:i:s', $timeAt) < '10:00:00') {
            $term = date('Ymd', $timeAt);
        } else {
            //其余时间
            $term = date('Ymd', strtotime('next Monday', $timeAt));
        }

        return $term;
    }

    /**
     * 生成第一个幸运(黑桃)号码(中奖号码)
     *
     * @param string $term
     *
     * @return int
     */
    public static function createWinningNumber($term)
    {
        $termMd5 = md5($term);
        $finalNumber = substr($termMd5, -1);
        if (is_numeric($finalNumber)) {
            $number = $finalNumber % 13 + 1;
        } else {
            $number = ord($finalNumber) % 13 + 1;
        }

        return $number;
    }
}
