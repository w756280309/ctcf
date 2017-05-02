<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

/**
 * Class BranchVisit
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property string $visitDate          拜访日期
 * @property string $recp_name          接待者姓名
 * @property string $content            内容
 * @property string $comment            评论
 */
class BranchVisit extends ActiveRecord
{
    public static function tableName()
    {
        return 'crm_branch_visit';
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }
}