<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

/**
 * Class Contact
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $type   电话类型 mobile手机号 land_line 固话
 * @property string $obfsNumber 混淆后的号码
 * @property string $encryptedNumber    加密后的手机号
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 */
class Contact extends ActiveRecord
{
    const TYPE_MOBILE = 'mobile';
    const TYPE_LAND_LINE = 'land_line';

    public static function tableName()
    {
        return 'crm_contract';
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