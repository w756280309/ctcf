<?php

namespace P2pl;

/**
 * 支付类接口
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
interface PaymentTxInterface
{
    public function getTxSn();
    public function getTxDate();
    public function getEpayUserId();
    public function getAmount();
}
