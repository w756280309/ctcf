<?php

namespace Xii\Crm\Model;


use common\utils\SecurityUtils;
use common\utils\StringUtils;
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
 *
 * @property string $number             联系方式，完整手机号或带区号的座机号
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
        if (empty($number)) {
            return null;
        }
        if (
            preg_match('/^\d{8}$/', $number)
            || preg_match('/^\d{7}$/', $number)
        ) {
            $number = '0577-'.$number;
        }
        return Contact::findOne(['encryptedNumber' => SecurityUtils::encrypt($number)]);
    }

    public static function findContactByAccountId($accountId)
    {
        return Contact::find()->where(['account_id' => $accountId]);
    }

    public static function initNew($number) {
        $contact = new Contact();

        if (
            preg_match('/^\d{8}$/', $number)
            || preg_match('/^\d{7}$/', $number)
        ) {
            $contact->type = Contact::TYPE_LANDLINE;
            $number = '0577-'.$number;
        } elseif(substr($number, 0, 1) === '0') {
            $contact->type = Contact::TYPE_LANDLINE;
        } else {
            $contact->type = Contact::TYPE_MOBILE;
        }
        $contact->number = $number;
        if ($contact->type === Contact::TYPE_MOBILE) {
            $contact->obfsNumber = StringUtils::obfsMobileNumber($number);
        } else {
            $contact->obfsNumber = StringUtils::obfsLandlineNumber($number);
        }
        $contact->encryptedNumber = SecurityUtils::encrypt($number);

        return $contact;
    }

    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }
}
