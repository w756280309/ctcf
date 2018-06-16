<?php

namespace common\event;

use yii\base\Event;

/**
 * 与用户有关的事件对象
 */
class OrderEvent extends Event
{
    public $order;
}
