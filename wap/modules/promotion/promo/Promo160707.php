<?php

namespace wap\modules\promotion\promo;

use Yii;
use common\models\user\User;


class Promo160707
{
    private $reward_config = [
        1 => [1, 3],
        2 => [4, 6],
        3 => [7, 50],
        4 => [51, 70],
        5 => [71, 80],
        6 => [81, 85],
        7 => [86, 97],
        8 => [98, 98],
        9 => [99, 100],
    ];

    private $reward_yitou_config = [
        3 => [1, 50],
        4 => [51, 70],
        5 => [71, 80],
        6 => [81, 85],
        7 => [86, 97],
        8 => [98, 98],
        9 => [99, 100],
    ];

    /**
     * 种类           中奖概率%（未投、已投）     起投金额
     * 1、28元代金券   3      0                  1000
     * 2、50元代金券   3      0                  10000
     * 3、90元代金券   44    50                  50000
     * 4、120元代金券  20    20                  100000
     * 5、180元代金券  10    10                  200000
     * 6、888元礼包    5      5                  28元1张，50元1张，90元3张，120元3张，180元1张
     * 7、再抽一次     12    12
     * 8、再接再励     1      1
     * 9、京东卡       2      2                  100元
     * @param User $user
     * @return int|void
     */
    public function drawReward(User $user)
    {
        $config = $user->UserIsInvested ? $this->reward_yitou_config : $this->reward_config;
        $pool = range(1, 100, 1);
        shuffle($pool);
        $number_sub = mt_rand(0, 99);
        $number = $pool[$number_sub];
        foreach ($config as $key => $value) {
            if ($number >= $value[0] && $number <= $value[1]) {
                return $key;
            }
        }
    }
}
