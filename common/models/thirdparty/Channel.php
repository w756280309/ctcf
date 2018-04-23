<?php

namespace common\models\thirdparty;

use yii\db\ActiveRecord;

class Channel extends ActiveRecord
{
    public function rules()
    {
        return [
            [['userId', 'thirdPartyUser_id'], 'required'],
            ['createTime', 'safe'],
        ];
    }
}