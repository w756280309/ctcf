<?php

namespace PayLog;

use common\models\TradeLog;

class PayLogUtils
{
    private $user;
    private $rq;
    private $rp;

    public function __construct(
        $user,
        $rq,
        $rp
    ) {
        $this->user = $user;
        $this->rq = $rq;
        $this->rp = $rp;
    }

    public function buildLog(){
        //记录日志
        $tradeLog = new TradeLog([
            'tx_code' => $this->rq->getTxCode(),
            'tx_sn' => $this->rq->getTxSn(),
            'pay_id' => 0,//默认
            'uid' => $this->user->id,
            'account_id' => $this->user->accountInfo->id,
            'request' => $this->rq->getXml(),
            'response_code' => $this->rp->getCode(),
            'response' => $this->rp->getText()
        ]);
        $tradeLog->save();
    }


}
