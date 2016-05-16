<?php

namespace common\event;

use yii\base\Event;

/**
 * 与用户有关的事件对象
 */
class UserEvent extends Event
{
    private $user;

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
}
