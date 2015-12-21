<?php

namespace PayGate\Cfca\Identity;

/**
 * 个人身份四要素.
 */
class IndividualIdentity
{
    const ID_TYPE_RESIDENT = 0; // 身份证

    private $realName; // 实名
    private $idType;   // 证件类型
    private $idNo;     // 证件号码
    private $mobile;   // 手机号

    public static function getValidIdTypes()
    {
        return [
            self::ID_TYPE_RESIDENT,
        ];
    }

    public function __construct($realName, $idType, $idNo, $mobile)
    {
        $this->realName = $realName;
        $this->idType = $idType;
        $this->idNo = $idNo;
        $this->mobile = $mobile;
    }

    public function getRealName()
    {
        return $this->realName;
    }

    public function getIdType()
    {
        return $this->idType;
    }

    public function getIdNo()
    {
        return $this->idNo;
    }

    public function getMobile()
    {
        return $this->mobile;
    }
}
