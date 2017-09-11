<?php

namespace common\models\promo;

use yii\db\ActiveRecord;
use Zii\Behavior\DateTimeBehavior;

/**
 * 周周乐、扑克牌活动
 */
class PokerUser extends ActiveRecord
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

    public function behaviors()
    {
        return [
            [
                'class' => DateTimeBehavior::className(),
                'createTimeAttribute' => 'createTime',
                'updateTimeAttribute' => 'updateTime',
            ],
        ];
    }

}