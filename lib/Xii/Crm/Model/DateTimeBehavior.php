<?php

namespace Xii\Crm\Model;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class DateTimeBehavior extends AttributeBehavior
{
    public $createTimeAttribute = 'createTime';
    public $updateTimeAttribute = 'updateTime';
    public $value;

    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->createTimeAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updateTimeAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if (null === $this->value) {
            if (
                $event->name === BaseActiveRecord::EVENT_BEFORE_INSERT
                && null !== $event->sender->getAttribute($this->createTimeAttribute)
            ) {
                return $event->sender->getAttribute($this->createTimeAttribute);
            }

            return date('Y-m-d H:i:s');
        }

        return parent::getValue($event);
    }
}
