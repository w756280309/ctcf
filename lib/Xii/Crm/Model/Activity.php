<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

/**
 * Class Activity
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property string $type               类型　phone_call/note
 * @property string $summary            概括
 * @property string $content            详细描述
 * @property string $comment            评论
 */
class Activity extends ActiveRecord
{
    const TYPE_PHONE_CALL = 'phone_call';//电话咨询
    const TYPE_NOTE = 'note';//备注
    const TYPE_RECEPTION = 'reception';//门店接待

    public static function tableName()
    {
        return 'crm_activity';
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
            'createTime' => '创建时间',
            'content' => '内容',
            'summary' => '备注',
        ];
    }
}