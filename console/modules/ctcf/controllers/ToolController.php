<?php

namespace console\modules\ctcf\controllers;

use Ding\DingNotify;
use yii\console\Controller;

/**
 * 工具脚本
 *
 * Class ToolController
 * @package console\controllers
 */
class ToolController extends Controller
{
    /**
     * 获取所有钉钉用户:工具脚本
     */
    public function actionGetCtcfDingUser()
    {
        $ding = new DingNotify();
        $data = $ding->getAllUser();
        $string = "[" . PHP_EOL;
        foreach ($data as $value) {
            $string .= "\t" . '{"name":"' . $value['name'] . '","userId":"' . $value['userid'] . '"},' . PHP_EOL;
        }
        $string = rtrim($string, ',' . PHP_EOL);
        $string .= PHP_EOL;
        $string .= "]" . PHP_EOL;
        echo $string;
    }

    /**
     * 获取钉钉组织架构:工具脚本
     */
    public function actionGetCtcfDingDepartment()
    {
        $ding = new DingNotify();
        $data = $ding->getDepartment();
        $string = "[" . PHP_EOL;
        foreach ($data as $value) {
            $string .= "\t" . '{"id":"' . $value['id'] . '","name":"' . $value['name'] . '"},' . PHP_EOL;
        }
        $string = rtrim($string, ',' . PHP_EOL);
        $string .= PHP_EOL;
        $string .= "]" . PHP_EOL;
        echo $string;
    }

    /**
     * 创建钉钉群:工具脚本
     */
    public function actionCreateCtcfDingChat()
    {
        $ding = new DingNotify();
        $data = $ding->chatCreate('楚天财富满标通知群', '012758001078576', ['CTCF00098', 'CTCF00094', '1700442815676250']);
        echo $data;
    }
}
