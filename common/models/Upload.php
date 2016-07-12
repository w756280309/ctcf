<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Upload extends ActiveRecord
{
    public static function tableName()
    {
        return "admin_upload";
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string'],
            ['title', 'string', 'max'=>15],
            [['allowHtml', 'isDeleted', 'created_at', 'updated_at'], 'integer'],
            ['link', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            ['link', 'file', 'skipOnEmpty' => true, 'maxSize' => 1048576, 'tooBig' => '图片大小不能超过1M'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '文件名',
            'link' => '文件地址',
            'allowHtml' => '允许套页面',
            'isDeleted' => '是否删除',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
