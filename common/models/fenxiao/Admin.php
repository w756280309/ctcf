<?php

namespace common\models\fenxiao;

use common\models\affiliation\Affiliator;
use Yii;

class Admin extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db_fin;
    }

    public function getAffiliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }
}