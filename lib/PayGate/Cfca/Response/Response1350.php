<?php

namespace PayGate\Cfca\Response;

/**
 * 结算
 * 结算查询1350响应.
 */
class Response1350 extends Response
{
    protected $serialNumber;//原结算交易流水号
    protected $orderNo;//对应支付单号
    protected $amount;//金额；单位分
    protected $remark;
    protected $accountType;//账户类型，
    protected $status;//结算状态10=已经受理30=正在结算40=已经执行(已发送转账指令)50=转账退回

    /**
     * 是否处理结束40是已经发送转账，50是转账退回.
     *
     * @return bool
     */
    public function isDone()
    {
        return 40 === $this->status
            || 50 === $this->status;
    }

    /**
     * 如果是状态为40代表结算成功
     *
     * @return bool
     */
    public function isSuccess()
    {
        return 40 === $this->status;
    }

    public function getSerializationData()
    {
        return [
            'orderNo' => $this->orderNo,
            'serialNumber' => $this->serialNumber,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'accountType' => $this->accountType,
            'status' => $this->status,
        ];
    }

    public function getOrderNo()
    {
        return $this->orderNo;
    }

    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getAccountType()
    {
        return $this->accountType;
    }

    protected function populate()
    {
        $this->orderNo = (string) $this->xmlObj->Body->OrderNo;
        $this->serialNumber = (string) $this->xmlObj->Body->SerialNumber;
        $this->amount = (int) $this->xmlObj->Body->Amount;
        $this->accountType = (int) $this->xmlObj->Body->AccountType;
        $this->status = (int) $this->xmlObj->Body->Status;
        $this->remark = (string) $this->xmlObj->Body->Remark;
    }
}
