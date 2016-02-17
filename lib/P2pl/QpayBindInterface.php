<?php

namespace P2pl;

interface QpayBindInterface
{
    public function getTxSn();
    public function getTxDate();
    public function getEpayUserId();
    public function getCardNo();
    public function getLegalName();
    public function getIdType();
    public function getIdNo();
}
