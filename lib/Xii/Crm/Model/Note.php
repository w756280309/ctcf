<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

/**
 * Class Note
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property string $content
 *
 */
class Note extends ActiveRecord
{
    public function rules()
    {
        return [
            ['content', 'required'],
            ['content', 'string'],
            ['content', 'trim'],
        ];
    }

    public static function tableName()
    {
        return 'crm_note';
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content' => '备注',
            'createTime' => '创建时间',
        ];
    }
}