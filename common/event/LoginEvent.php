<?php

namespace common\event;

use yii\base\Event;

class LoginEvent extends Event
{
    public $loginId;
    public $password;
    public $user;
}
