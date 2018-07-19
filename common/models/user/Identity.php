<?php

namespace common\models\user;

use yii\db\ActiveRecord;

class Identity extends ActiveRecord
{
    public static function tableName()
    {
        return 'identity';
    }

    public function rules()
    {
        return [
            [['encryptedIdCard', 'create_time'], 'required'],
            ['encryptedIdCard', 'unique'],
        ];
    }
}
