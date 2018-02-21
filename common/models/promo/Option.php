<?php

namespace common\models\promo;

use yii\db\ActiveRecord;
use Zii\Behavior\DateTimeBehavior;

class Option extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => DateTimeBehavior::className(),
                'createTimeAttribute' => 'createTime',
                'updateTimeAttribute' => 'updateTime',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['questionId', 'content'], 'required'],
            ['questionId', 'integer'],
            ['content', 'string'],
        ];
    }
}