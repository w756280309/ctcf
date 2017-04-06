<?php

namespace console\command;


use Ding\DingNotify;

/**
 * 实名认证失败通知类
 */
class IdentityFailNotifyCommand extends BaseCommand
{
    /**
     * 队列处理程序
     * @param $data
     * @return bool
     */
    public function doMyJob($data = [])
    {
        return (new DingNotify('wdjf'))->sendToUsers('用户[ID: ' . $data['userId'] . ']，于' . $data['dateTime'] . ' 进行开户操作，操作失败，失败信息，' . $data['message']);
    }
}