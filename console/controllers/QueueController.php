<?php

namespace console\controllers;


use common\models\queue\QueueTask;
use console\command\IdentityFailNotifyCommand;
use console\command\ProcessCommand;
use console\command\RechargeFailNotifyCommand;
use console\command\WorkerCommand;
use yii\console\Controller;

class QueueController extends Controller
{
    public function actions()
    {
        return [
            'worker' => WorkerCommand::className(),
            'process' => ProcessCommand::className(),
        ];
    }
}