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
 * @property string $ref_type
 * @property int    $ref_id
 */
class Activity extends ActiveRecord
{
    const TYPE_PHONE_CALL = 'phone_call';//电话咨询
    const TYPE_NOTE = 'note';//备注
    const TYPE_BRANCH_VISIT = 'branch_visit';//门店接待

    public static function getRefTypeList()
    {
        return [
            self::TYPE_NOTE,
            self::TYPE_PHONE_CALL,
            self::TYPE_BRANCH_VISIT,
        ];
    }

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

    public function getRefObj()
    {
        switch ($this->ref_type) {
            case Activity::TYPE_NOTE:
                $obj = Note::findOne($this->ref_id);
                break;
            case Activity::TYPE_PHONE_CALL:
                $obj = PhoneCall::findOne($this->ref_id);
                break;
            case Activity::TYPE_BRANCH_VISIT:
                $obj = BranchVisit::findOne($this->ref_id);
                break;
            default:
                $obj = null;
        }

        return $obj;
    }
}