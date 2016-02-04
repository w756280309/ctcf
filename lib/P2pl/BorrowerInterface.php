<?php

namespace P2pl;

interface BorrowerInterface
{
    public function getLoanUserId();//融资人在平台方的id
    public function getLoanAccountId();// N融资方在联动的账户id
    public function getLoanAccountType();// N借款方账户类型 01 个人 02商户
}
