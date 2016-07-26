<?php

namespace common\view;

use yii\web\View;

class WapView extends View
{
    public $backUrl = null;
    public $showBottomNav = false;
    public $showAvatar = false;
    public $showIndexBottomNav = false;   //首页页尾是否需要显示开关
    public $header_nav_on = false;        //导航条显示开关
}
