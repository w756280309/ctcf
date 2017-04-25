<?php

namespace Xii\Crm\Model;

use common\models\user\User;
use yii\db\ActiveRecord;

/**
 * Class Account
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $creator_id
 * @property int    $primaryContact_id  默认联系方式ＩＤ
 * @property bool   $isConverted        是否被转化(是否在温都注册)
 * @property string $type               账户类型 person:未转化用户
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 */
class Account extends ActiveRecord
{
    const TYPE_PERSON = 'person';

    public static function tableName()
    {
        return 'crm_account';
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }

    public function getIdentity()
    {
        $identity = User::findOne([
            'crmAccount_id' => $this->id,
        ]);

        if (null === $identity) {
            $identity = Identity::findOne([
                'account_id' => $this->id,
            ]);
        }

        return $identity;
    }
}