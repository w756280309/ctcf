<?php

namespace common\view;

use yii\web\View;

class BackendView extends View
{
    public $loadAuthJs = true; //是否加载公用的ajax.js脚本开关,该脚本主要用于Ajax请求时,如果所请求链接没有配置权限,向用户提示错误信息
}