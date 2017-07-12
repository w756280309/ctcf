<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class NotifyLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'notifylog';
    }

    public function behaviors()
    {
        return [
             TimestampBehavior::className(),
        ];
    }
}
