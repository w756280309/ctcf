<?php

namespace common\jobs;

use yii\base\Object;
use yii\queue\Job;

class SyncOnlineJob extends Object implements Job
{
    public $mobile;
    public $name;
    public $idCard;
    public $crmAccountId;
    public $inviterId;
    public $affiliatorId;

    public function execute($queue)
    {

    }
}