<?php

namespace console\command;


use Ding\DingNotify;

/**
 * 充值失败通知类
 */
class RechargeFailNotifyCommand extends BaseCommand
{
    /**
     * 队列处理程序
     * @param $data
     * @return bool
     */
    public function doMyJob($data)
    {
        return (new DingNotify('wdjf'))->sendToUsers('用户[ID: ' . $data['userId'] . ']，于' . $data['dateTime'] . ' 进行充值操作，操作失败，充值订单 :'.$data['rechargeSn'].' ，失败信息，' . $data['message']);
    }
}