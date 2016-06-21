<?php

$this->title = "新手引导";

$this->registerCssFile(ASSETS_BASE_URI.'css/leadpage/leadpage.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/leadpage/leadpage.js', ['depends' => 'frontend\assets\FrontAsset']);

?>

<div class="container-leadpage">
    <div class="change-box">
        <div class="change-single change-selected">
            <div class="change-left">
                <p class="change-click">第1步：注册</p>
            </div>
        </div>
        <div class="change-single">
            <div class="change-center">
                <p class="change-click">第2步：认证</p>
            </div>
        </div>
        <div class="change-single">
            <div class="change-center">
                <p class="change-click">第3步：充值</p>
            </div>
        </div>
        <div class="change-single">
            <div class="change-center">
                <p class="change-click">第4步：投资</p>
            </div>
        </div>
        <div class="change-single">
            <div class="change-center">
                <p class="change-click">第5步：回款</p>
            </div>
        </div>
        <div class="change-single">
            <div class="change-right">
                <p class="change-click">第6步：提现</p>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="container-box">
        <p class="tip-font">用户进入首页，<span>点击【注册】</span>，输入手机号和设置登录密码，完成注册</p>
        <a class="container-none"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_1.png" width="649" alt="">
        </div>
        <a class="container-right"></a>
    </div>
    <div class="container-box hidden">
        <p class="tip-font">用户注册成功后，<span>点击【充值】</span>，进入开通资金托管账户页面，<span>完成实名认证</span></p>
        <a class="container-left"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_2.png" width="649" alt="">
        </div>
        <a class="container-right"></a>
    </div>
    <div class="container-box hidden">
        <p class="tip-font">进入【账户】，<span>点击【充值】</span>，按照指引完成绑卡和充值</p>
        <a class="container-left"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_3.png" width="649" alt="">
        </div>
        <a class="container-right"></a>
    </div>
    <div class="container-box hidden">
        <p class="tip-font"><span>进入【理财】</span>，选择适合您的投资项目，按页面指引按成认购</p>
        <a class="container-left"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_4.png" width="649" alt="">
        </div>
        <a class="container-right"></a>
    </div>
    <div class="container-box hidden">
        <p class="tip-font">投资完成后，可到【账户】><span>【我的理财】</span>中，查看您的认购项目</p>
        <a class="container-left"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_5.png" width="649" alt="">
        </div>
        <a class="container-right"></a>
    </div>
    <div class="container-box hidden">
        <p class="tip-font">登录【账户】，<span>点击【提现】</span>，可将金额提现至绑定的银行卡</p>
        <a class="container-left"></a>
        <div class="container-center">
            <img src="../../images/leadpage/display_6.png" width="649" alt="">
        </div>
        <a class="container-none"></a>
    </div>
    <div class="clear"></div>
    <?php if (\Yii::$app->getUser()->isGuest) { ?>
    <a class="link-register" href="">立即注册</a>
    <p class="bottom-font">1分钟成为温都金服会员，轻松开始理财</p>
    <?php } ?>
</div>
