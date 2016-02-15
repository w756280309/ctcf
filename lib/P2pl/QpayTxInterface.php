<?php

namespace P2pl;

interface QpayTxInterface
{
    public function getTxSn();//充值单号
    public function getTxDate();//商户生成订单的日期
    public function getEpayUserId();//托管平台用户号
    public function getAmount();//以分为单位
    public function getClientIp();
}
