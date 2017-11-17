<?php

namespace Zii\Model;

use common\service\UserService;

trait LevelTrait
{
    /**
     * 计算用户会员等级.
     *
     * @return int 用户会员等级,目前是0-7.
     */
    public function getLevel()
    {
        return UserService::calcUserLevel($this->coins);
    }
}