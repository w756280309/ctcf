<?php

namespace common\models\salebranch;

use yii\db\ActiveRecord;

class SaleBranch extends ActiveRecord
{

    public function rules()
    {
        return [
            ['branchName', 'required'],
            ['branchName', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branchName' => '网点',
        ];
    }
}
