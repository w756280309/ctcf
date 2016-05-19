<?php

namespace P2pl;

/**
 * 用户投标接口
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
interface OrderTxInterface
{
    public function getLoanId();//标的号
    public function getTxSn();
    public function getTxDate();
    public function getEpayUserId();
    public function getAmount();
    public function getPaymentAmount();
}
