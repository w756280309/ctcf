<?php

namespace PayGate\Cfca\Response;

/**
 * 支付查询
 * 支付查询1320响应
 */
class Response1320 extends Response
{
    protected $paymentNo;//充值单号
    protected $amount;//金额；单位分
    protected $remark;
    protected $status;//支付状态10=未支付20=已经支付
    protected $bankNotificationTime;//支付平台收到银行通知时间，格式：YYYYMMDDhhmmss

    /**
     * 如果是状态为20代表充值成功
     * @return bool
     */
    public function isSuccess(){
        return 20 === $this->status;
    }

    public function getSerializationData()
    {
        return [
            'paymentNo' => $this->paymentNo,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'bankNotificationTime' => $this->bankNotificationTime,
            'status' => $this->status
        ];
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getBankNotificationTime()
    {
        return $this->bankNotificationTime;
    }

    protected function populate()
    {
        $this->paymentNo = (string) $this->xmlObj->Body->PaymentNo;
        $this->amount = bcdiv($this->xmlObj->Body->Amount, 100);//将分制转为元制单位
        $this->remark = (string) $this->xmlObj->Body->Remark;
        $this->status = (int) $this->xmlObj->Body->Status;
        $this->bankNotificationTime = (string) $this->xmlObj->Body->BankNotificationTime;
    }
}
