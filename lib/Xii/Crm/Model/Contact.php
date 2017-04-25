<?php

namespace Xii\Crm\Model;


use common\utils\SecurityUtils;
use yii\db\ActiveRecord;

/**
 * Class Contact
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $type   电话类型 mobile手机号 landline 固话
 * @property string $obfsNumber 混淆后的号码
 * @property string $encryptedNumber    加密后的手机号
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 */
class Contact extends ActiveRecord
{
    const TYPE_MOBILE = 'mobile';
    const TYPE_LANDLINE = 'landline';

    public $number;

    public static function tableName()
    {
        return 'crm_contact';
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
            'type' => '账号类型',
            'number' => '号码',
        ];
    }

    public function getNumber()
    {
        if (!$this->number) {
            $this->number = SecurityUtils::decrypt($this->encryptedNumber);
        }

        return $this->number;
    }

    public static function getTypeLabels()
    {
        return [
            self::TYPE_MOBILE => '手机号',
            self::TYPE_LANDLINE => '座机',
        ];
    }

    public static function fetchOneByNumber($number)
    {
        return Contact::findOne(['encryptedNumber' => SecurityUtils::encrypt($number)]);
    }


}
