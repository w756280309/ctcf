<?php

namespace common\event;

use yii\base\Event;

class RepayEvent extends Event
{
    public $loan;
    public $term;
}