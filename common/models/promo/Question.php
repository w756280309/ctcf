<?php

namespace common\models\promo;

use yii\db\ActiveRecord;
use Zii\Behavior\DateTimeBehavior;

/**
 * Class Question
 * @package common\models\promo
 *
 * @property string $title 题目
 * @property string $batchSn 批次号
 * @property null|integer $promoId 活动ID
 * @property string $answer 答案，当为多选一时，存optionId
 * @property string $createTime 创建时间
 * @property string $updateTime 修改时间
 */
class Question extends ActiveRecord
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
            [['title', 'batchSn'], 'required'],
            ['promoId', 'integer'],
        ];
    }
}