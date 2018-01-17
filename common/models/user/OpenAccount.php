<?php

namespace common\models\user;


use common\utils\SecurityUtils;
use common\utils\TxUtils;
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
 * @property string $message            错误信息
 * @property string $code               联动返回状态码
 * @property string $createTime
 * @property string $updateTime
 * @property string $sn
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
            'encryptedIdCard' => SecurityUtils::encrypt(strtoupper($userIdentity->idcard)),
            'status' => self::STATUS_INIT,
            'sn' => TxUtils::generateSn("REG"),
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

    //获取向用户展示的提示信息
    public function getPromptMessage()
    {
        if (!is_null($this->code) && in_array($this->code, ['00290502', '00160080', '00240632', '00160079', '00290501', '00160104', '00060022', '101'])) {
            if ($this->code === '00240632') {
                $message = '您请求太频繁了，请1分钟后重试!';
            } else {
                $message = $this->message;
            }
        } else {
            $message = '';
        }
        return $message;
    }
}