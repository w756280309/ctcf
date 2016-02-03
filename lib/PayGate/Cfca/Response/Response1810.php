<?php

namespace PayGate\Cfca\Response;

/**
 * 批量代付查询响应.
 */
class Response1810 extends Response
{
    protected $txs;
    /**
     * 获取相应返回的所有项目.
     */
    public function getTxs()
    {
        return $this->txs;
    }

    public function getSerializationData()
    {
        return [
            'txs' => $this->txs,
        ];
    }

    /**
     * 通过while(list( , $node) = each($nodes)){}获取所有对象
     */
    protected function populate()
    {
        $data = $this->xmlObj->xpath('/Response/Body/Tx');
        $nodes = array();
        foreach ($data as $node) {
            $nodes[] = [
                'TxType' => (string) $node->TxType,
                'TxSn' => (string) $node->TxSn,
                'TxAmount' => (int) $node->TxAmount,
                'InstitutionAmount' => (int) $node->InstitutionAmount,
                'PaymentAmount' => (int) $node->PaymentAmount,
                'InstitutionFee' => (int) $node->InstitutionFee,
                'Remark' => (string) $node->Remark,
                'BankNotificationTime' => (string) $node->BankNotificationTime,
            ];
        }
        $this->txs = $nodes;
    }
}
