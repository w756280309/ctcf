<?php

namespace Lhjx\Identity;

interface VerificationInterface
{
    public function getName();          //获取用户名
    public function getIdCardNumber();  //获取身份证号
    public function getIdentity();      //获取identity属性
}
