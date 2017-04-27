<?php

namespace Xii\Crm\Model;


use common\utils\SecurityUtils;
use common\utils\StringUtils;
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
 * @property string $name               姓名
 *
 */
class Identity extends ActiveRecord
{
    public $name;

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

    /**
     * 获得crm的姓名
     *
     * todo 临时代码-混淆姓名的字段不存在，先返回原名
     */
    public function getCrmName()
    {
        return $this->obfsName;
    }

    /**
     * 获得crm的gender
     */
    public function getCrmGender()
    {
        return $this->gender;
    }

    /**
     * 获得crm的年龄
     */
    public function getCrmAge()
    {
        $age = null;
        if ($this->birthYear) {
            $age = date('Y') - (int) $this->birthYear;
        }

        return $age;
    }

    public function getName()
    {
        if (is_null($this->name)) {
             $this->name = SecurityUtils::decrypt($this->encryptedName);
        }

        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->obfsName = StringUtils::obfsName($name);
        $this->encryptedName = SecurityUtils::encrypt($name);
    }
}
