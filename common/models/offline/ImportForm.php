<?php

namespace common\models\offline;

use yii\base\Model;

class ImportForm extends Model
{
    public $excel;

    public function rules()
    {
        return [
            ['excel', 'file'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'excel' => 'excel',
        ];
    }
}