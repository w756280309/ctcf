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
      
    /**
     * 获取相应返回的所有项目
     */
    public function getItems(){
        
    }
    
    /**
     * 通过某一项来判断处理是否完结
     * @param type $item
     * @return bool
     */
    public function isDone($item)
    {
        return 30 === $item->Status
            || 40 === $item->Status;
    }
    
    /**
     * 通过某一项来判断处理是否成功
     * @param type $item
     * @return bool
     */
    public function isSuccess($item)
    {
        return 30 === $item->Status;
    }
    
    public function getSerializationData()
    {
    }


    protected function populate()
    {
    }
    
}
