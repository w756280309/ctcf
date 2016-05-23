<?php

namespace wap\modules\promotion\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ranking_promo".
 *
 * @property integer $id
 * @property string $title
 * @property integer $startAt
 * @property integer $endAt
 */
class RankingPromo extends ActiveRecord
{
    public static function tableName()
    {
        return 'ranking_promo';
    }

    public function rules()
    {
        return [
            [['title', 'startAt', 'endAt'], 'required'],
            [['startAt', 'endAt'], 'string'],
            [['title'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '活动名称',
            'startAt' => '开始时间',
            'endAt' => '结束时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_string($this->startAt)) {
                $this->startAt = strtotime($this->startAt);
            }
            if (is_string($this->endAt)) {
                $this->endAt = strtotime($this->endAt);
            }
            return true;
        } else {
            return false;
        }
    }
}
