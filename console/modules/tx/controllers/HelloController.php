<?php

namespace console\modules\tx\controllers;

use yii\console\Controller;

class HelloController extends Controller
{
    /**
     * 这是命令行指令示例，回显调用时的第一个参数.
     *
     * @param string $message 回显内容
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message."\n";
    }
}
