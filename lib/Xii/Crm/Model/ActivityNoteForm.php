<?php

namespace Xii\Crm\Model;


use yii\base\Model;

//客户备注表单
class ActivityNoteForm extends Model
{
    public $summary;

    public function rules()
    {
        return [
            ['summary', 'required'],
            ['summary', 'trim'],
            ['summary', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'summary' => '备注',
        ];
    }
}