<?php

namespace common\models\fenxiao;

use Yii;

class Admin extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db_fin;
    }
}