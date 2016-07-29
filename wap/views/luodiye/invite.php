<?php

$this->title = "温州报业传媒旗下理财平台";
$this->params['breadcrumbs'][] = $this->title;
$this->header_nav_on = true;

$this->registerCssFile(ASSETS_BASE_URI.'css/first.css', ['depends' => 'wap\assets\WapAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/invite/activedisplay.css', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/invite/activedisplay.js', ['depends' => 'wap\assets\WapAsset']);

?>
<div class="row transform-box">
    <div class="col-xs-12 transform-front-box">
        <div class="logo-back">
            <img src="../../images/invite/logo-back.png" alt="">
        </div>
        <div class="front-font">
            <p>好友邀请送好礼</p>
            <p>快来领取吧！</p>
        </div>
        <div class="transform-rotate">
            <img src="../../images/invite/transform-back.png" alt="">
            <div class="transform-absolute">
                <img id="transform-icon" src="../../images/invite/transform-icon.png" alt="">
            </div>
        </div>
    </div>
    <div class="col-xs-12 transform-back-box">
        <div class="transform-top">
            <img src="../../images/invite/transform-complete.png" alt="">
        </div>
        <div class="transform-center">
            <p class="p-tip">恭喜您获得代金券！</p>
            <p class="p-number">50元</p>
            <p class="p-use">投资即可使用</p>
        </div>
        <div class="transform-bottom">
            <a href="/site/signup/">立即领取</a>
        </div>
    </div>
</div>
<div class="row description-box">
    <p class="description-header"><span>什么是温都金服？</span></p>
    <p class="description-content">温州温都金融信息服务股份有限公司简称“温都金服”，隶属温州报业传媒旗下的理财平台。甄选各类金融机构、优质企业理财产品。提供银行级安全理财服务，保障用户资金安全，安享稳健高收益。</p>
</div>
<div class="row production-box">
    <p class="production-header">精品理财</p>
    <div class="licai-img">
        <div class="col-xs-6 licai-img">
            <a href="/deal/deal/index/">
                <img src="../../images/luodiye/production-left.png" alt="温盈金">
            </a>
        </div>
        <div class="col-xs-6 licai-img">
            <a href="/deal/deal/index/">
                <img src="../../images/luodiye/production-right.png" alt="温盈宝">
            </a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="row choose-box">
    <h3>为什么选择温都金服？</h3>
    <div class="choose-content">
        <img src="../../images/luodiye/choose-top.png" alt="">
        <img src="../../images/luodiye/choose-bottom.png" alt="">
    </div>
</div>
<a class="link-last" href="/deal/deal/index/">立即赚钱</a>

<div class="fixed-float">
    <img src="../../images/luodiye/fixed-float.png" alt="">
</div>
<div class="fixed-box">
    <div class="fixed-outside">
        <div class="fixed-opacity"><img src="../../images/luodiye/fixed-float.png" alt=""></div>
        <table class="fixed-content">
            <tr>
                <td colspan="3" class="table-img"><img src="../../images/luodiye/fixed-float.png" alt=""></td>
            </tr>
            <tr class="table-content">
                <td width="600"><p class="content-font">使用APP客户端，理财随时随地！</p></td>
                <td width="300"><a class="content-link" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf" target="_self">立即下载</a></td>
                <td width="200">
                    <a class="content-picture"><img src="../../images/luodiye/close-icon-height.png" alt=""></a>
                </td>
            </tr>
        </table>
    </div>
</div>

