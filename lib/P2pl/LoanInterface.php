<?php

namespace P2pl;

interface LoanInterface
{
    public function getLoanId();//标的号
    public function getLoanName();//标的名称
    public function getLoanAmount();//标的金额
    public function getLoanExpireDate();// N标的有效期，格式:YYYYMMDD，期望的满标日期
    public function getAltRepayerId(); //标的代偿方联动用户ID
    public function getFundReceiverId(); //标的用款方联动用户ID
}
