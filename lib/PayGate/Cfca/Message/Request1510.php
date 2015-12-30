<?php

namespace PayGate\Cfca\Message;

use Yii;

/**
 * 批量代付
 * 将批量代付的明细通过接口传给支付平台【后台放款时触发】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，
 */
class Request1510 extends AbstractRequest {

    private $batch;//批量代付的对象
    private $batchSn;//批量代付代码
    private $batchItem;//批量代付包含批次数据


    public function __construct(
    $institutionId, $batch
    ) {
        $this->batch = $batch;
        $this->batchSn = $batch->sn;
        $this->batchItem = $batch->items;
        parent::__construct($institutionId, 1510);
    }

    /**
     * 返回批次数据
     * @return array
     */
    public function getBatchItem(){
        return $this->batchItem;
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
