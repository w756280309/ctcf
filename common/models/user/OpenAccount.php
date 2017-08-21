<?php

namespace common\models\user;


use common\utils\SecurityUtils;
use yii\db\ActiveRecord;
use Zii\Behavior\DateTimeBehavior;

/**
 * Class OpenAccount
 * @package common\models\user
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $encryptedName      加密后的信命
 * @property string $encryptedIdCard    加密后的身份证号
 * @property string $status             状态
 * @property string $ip                 实名ip
 * @property string $createTime
 * @property string $updateTime
 */
class OpenAccount extends ActiveRecord
{
    private $name;
    private $idCard;

    CONST STATUS_INIT = 'init';
    CONST STATUS_SUCCESS = 'success';
    CONST STATUS_FAIL = 'fail';

    public static function tableName()
    {
        return 'open_account';
    }

    public function behaviors()
    {
        return [
            DateTimeBehavior::className(),
        ];
    }

    public static function initNew(User $user, UserIdentity $userIdentity)
    {
        return new self([
            'user_id' => $user->id,
            'encryptedName' => SecurityUtils::encrypt($userIdentity->real_name),
            'encryptedIdCard' => SecurityUtils::encrypt($userIdentity->idcard),
            'status' => self::STATUS_INIT,
        ]);
    }

    public function getName()
    {
        if (is_null($this->name)) {
            $this->name = SecurityUtils::decrypt($this->encryptedName);
        }
        return $this->name;
    }

    public function getIdCard()
    {
        if (is_null($this->idCard)) {
            $this->idCard = SecurityUtils::decrypt($this->encryptedIdCard);
        }
        return $this->idCard;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}