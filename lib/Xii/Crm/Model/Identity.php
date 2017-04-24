<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

/**
 * Class Identity
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property string $birthDate          生日
 * @property string $birthYear          出生年份
 * @property string $gender             性别 null;f:女性;m:男性
 * @property string $obfsName           混淆后的姓名
 * @property string $encryptedName      加密后的姓名
 * @property string $obfsIdNo           混淆后的身份证
 * @property string $encryptedIdNo      加密后的身份证
 *
 */
class Identity extends ActiveRecord
{
    public static function tableName()
    {
        return 'crm_identity';
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