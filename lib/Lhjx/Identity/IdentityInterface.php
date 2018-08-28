<?php

namespace Lhjx\Identity;

interface IdentityInterface
{
    public function getName();          //获取用户名
    public function getIdCardNumber();  //获取身份证号
}
