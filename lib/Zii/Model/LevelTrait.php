<?php

namespace Zii\Model;

trait LevelTrait
{
    /**
     * 计算用户会员等级.
     *
     * @return int 用户会员等级,目前是0-7.
     */
    public function getLevel()
    {
        $level = 0;
        $coins = $this->coins;

        if ($coins >= 20 && $coins < 50) {
            $level = 1;
        } elseif ($coins >= 50 && $coins < 100) {
            $level = 2;
        } elseif ($coins >= 100 && $coins < 200) {
            $level = 3;
        } elseif ($coins >= 200 && $coins < 500) {
            $level = 4;
        } elseif ($coins >= 500 && $coins < 800) {
            $level = 5;
        } elseif ($coins >= 800 && $coins < 1500) {
            $level = 6;
        } elseif ($coins >= 1500) {
            $level = 7;
        }

        return $level;
    }
}