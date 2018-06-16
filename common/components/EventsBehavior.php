<?php

namespace common\components;

use yii\base\Behavior;

class EventsBehavior extends Behavior
{
    public $events;

    public function attach($owner)
    {
        $this->owner = $owner;
        foreach ($this->events as $event => $handlers) {
            foreach ($handlers as $handler) {
                $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
            }
        }
    }

}
