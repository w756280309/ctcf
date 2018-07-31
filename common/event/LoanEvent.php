<?php

namespace common\event;

use yii\base\Event;

class LoanEvent extends Event
{
    public $loan; //标的对象
}