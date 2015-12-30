<?php

namespace PayGate\Cfca\Message;

use Yii;

/**
 * 批量代付
 * 将批量代付的明细通过接口传给支付平台【后台放款时触发】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，批次对象
 */
class Request1510 extends AbstractRequest {

    private $batchSn;//批量代付代码

    public function __construct(
    $institutionId, $batch
    ) {
        $this->batchSn = $batch->sn;
        parent::__construct($institutionId, 1510);
    }

    /**
     * 批量代付批次号
     * @return string
     */
    public function getBatchSn() {
        return $this->batchSn;
    }

    /**
     * 用作日志记录时候通用的方法，批量代付批次号
     * @return string
     */
    public function getTxSn() {
        return $this->batchSn;
    }

    public function getXml() {
    }

}
