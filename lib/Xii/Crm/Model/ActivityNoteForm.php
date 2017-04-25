<?php

namespace Xii\Crm\Model;


use yii\base\Model;

//客户备注表单
class ActivityNoteForm extends Model
{
    public $content;

    public function rules()
    {
        return [
            ['content', 'required'],
            ['content', 'trim'],
            ['content', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content' => '备注',
        ];
    }
}