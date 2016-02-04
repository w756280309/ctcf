<?php

namespace P2pl;

/**
 * 借款人类
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class Borrower implements BorrowerInterface {
    private $loanUserId;
    private $loanAccountId;
    private $loanAccType;
    
    public function __construct($loanUserId, $loanAccountId = null, $loanAccType = null) {
        $this->loanUserId = $loanUserId;
        $this->loanAccountId = $loanAccountId;
        $this->loanAccType = $loanAccType;
    }

    //融资人在平台方的id
    public function getLoanUserId() {
        return $this->loanUserId;
    }

    // N融资方在联动的账户id
    public function getLoanAccountId() {
        return $this->loanAccountId;
    }

    // N借款方账户类型 01 个人 02商户
    public function getLoanAccType() {
        return $this->loanAccType;
    }

}
