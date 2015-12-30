<?php

namespace PayGate\Cfca\Message;

use Yii;

/**
 * 批量代付查询
 * 查询批量代付详情【定时任务查询】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，批次对象
 */
class Request1520 extends AbstractRequest {

    private $batchSn;//批量代付代码

    public function __construct(
    $institutionId, $batchsn
    ) {
        $this->batchSn = $batchsn;
        parent::__construct($institutionId, 1520);
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
