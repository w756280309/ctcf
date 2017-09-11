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
            $term = date('Ymd');
        } else {
            //其余时间
            $term = date('Ymd', strtotime('next Monday'));
        }

        return $term;
    }
}
