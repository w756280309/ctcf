<?php

namespace common\models\user;

use common\utils\SecurityUtils;
use yii\db\ActiveRecord;

class Identity extends ActiveRecord
{
    public static function tableName()
    {
        return 'identity';
    }

    public function rules()
    {
        return [
            [['encryptedName', 'encryptedIdCard', 'create_time'], 'required'],
            ['encryptedIdCard', 'unique'],
        ];
    }

    public function getName()
    {
        return SecurityUtils::decrypt($this->encryptedName);
    }

    public function getIdCardNumber()
    {
        return SecurityUtils::decrypt($this->encryptedIdCard);
    }

    public function setName($name)
    {
        $this->encryptedName = SecurityUtils::encrypt($name);
    }

    public function setIdCardNumber($idCardNum)
    {
        $this->encryptedIdCard = SecurityUtils::encrypt(strtoupper($idCardNum));
    }
}
