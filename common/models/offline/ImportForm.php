<?php

namespace common\models\offline;

use yii\base\Model;

/**
 * 此Model类目前用于线下数据->导入新数据
 */
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