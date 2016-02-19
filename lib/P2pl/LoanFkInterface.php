<?php

namespace P2pl;

/**
 * 放款接口
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
interface LoanFkInterface
{
    public function getTxSn();
    public function getTxDate();
    public function getLoanId();
    public function getAmount();//标的实际融资总额
    public function getBorrowerId();
}
