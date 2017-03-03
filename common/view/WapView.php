<?php

namespace common\view;

use yii\web\View;

class WapView extends View
{
    public $backUrl = null;
    public $showBottomNav = false;
    public $showAvatar = false;
    public $showIndexBottomNav = false;   //首页页尾是否需要显示开关
    public $headerNavOn = false;          //导航条显示开关
    public $hideHeaderNav = false;        //隐藏页头导航条
    public $share = null;                 //微信分享对象
    public $extraKeywords = '';           //附加KEYWORD
    public $replaceUrl = null;            //通过replace的方式后退跳转
    public $showViewport = true;          //fe框架中的viewport meta标签显示开关
}
