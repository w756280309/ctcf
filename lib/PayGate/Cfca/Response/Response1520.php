<?php

namespace PayGate\Cfca\Response;

/**
 * 批量代付查询响应
 */
class Response1520 extends Response
{
    protected $batchSn;
    protected $totalAmount;
    protected $totalCount;
    protected $remark;
    protected $items;


    /**
     * 获取相应返回的所有项目
     */
    public function getItems(){
        return $this->items;
    }
    
    /**
     * 通过某一项来判断处理是否完结
     * @param type $item
     * @return bool
     */
    public function isDone($item)
    {
        return 30 === $item['Status']
            || 40 === $item['Status'];
    }
    
    /**
     * 通过某一项来判断处理是否成功
     * @param type $item
     * @return bool
     */
    public function isSuccess($item)
    {
        return 30 === $item['Status'];
    }
    
    public function getSerializationData()
    {
        return [
            'items' => $this->items
        ];
    }


    protected function populate()
    {
        $data = $this->xmlObj->xpath('/Response/Body/Item');
        $nodes = array();
        foreach ($data as $node) {
            $nodes[] = [
                'ItemNo' => (string) $node->ItemNo,
                'BankID' => (string) $node->BankID,
                'AccountType' => (int) $node->AccountType,
                'AccountName' => (string) $node->InstitutionAmount,
                'AccountNumber' => (string) $node->AccountNumber,
                'Amount' => (int) $node->Amount,
                'Status' => (int) $node->Status,
                'BankTxTime' => (string) $node->BankTxTime,
                'ResponseCode' => (string) $node->ResponseCode,
                'ResponseMessage' => (string) $node->ResponseMessage
            ];
        }
        $this->items = $nodes;
    }
    
}
