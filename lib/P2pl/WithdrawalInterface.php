<?php

namespace P2pl;

/**
 * 提现接口
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
interface WithdrawalInterface
{
    public function getTxSn();
    public function getTxDate();
    public function getEpayUserId();
    public function getAmount();
}
