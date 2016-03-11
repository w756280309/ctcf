<?php
$this->title="帮助中心";
$this->registerJsFile('/js/helpcenter.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link href="/css/informationAndHelp.css" rel="stylesheet">
<style>
    body {
        background-color: #fff;
    }
</style>

<div class="container bootstrap-common helpcenter_login_resister">
        <!-- 主体 -->
                <div class="row">
                        <div class="col-xs-12">
                                <p class="header"><span>——————</span>&nbsp;注册登录篇&nbsp;<span>——————</span></p>
                        </div>
                </div>
        <div class="kong-width">
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">1.设置登录密码有什么要求？</p>
                                <p class="content">a. 6-20个数字+英文字符；</p>
                                <p class="content">b. 只能包含字母、数字以及标点符号（除空格）；</p>
                                <p class="content">c. 字母、数字以及标点符号至少包含2种。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">2.对注册时填写的手机号码有什么要求？</p>
                                <p class="content">您注册时使用的手机号码必须是大陆地区电信运营商支持的
                                        号段，且不支持小灵通。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">3.注册成功后可以更换手机号码吗？</p>
                                <p class="content">作为用户在温都金服平台和第三方资金托管账户重要的识别信息，手机号码无法进行变更。请用户妥善保管注册时使用的手机号码。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">4.注册个人用户时需要进行实名认证吗？</p>
                                <p class="content">
                                        注册温都金服账户时不需要对用户进行实名认证。但是当用户开通第三方资金托管平台（联动优势）账户时，需对用户进行实名身份认证，用户真实身份在该平台一经核实，不能修改。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">5.如何更改登录密码？</p>
                                <p class="content">登录温都金服账户，进入【账户】，在【 安全中心】，点击修改登录密码，按相关提示设置新密码即可。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">6.忘记登录密码怎么办？</p>
                                <p class="content">网站提供助自助找回密码，请您在登录页面点击“忘记密码”
                                        按钮，按照页面提示操作即可。</p>
                        </div>
                </div>
                <div class="row single">
                        <div class="col-xs-12">
                                <p class="single-title">7.账户被锁定怎么办？</p>
                                <p class="content">若账户被锁定无法正常登录，请拨打客服热线<?= Yii::$app->params['contact_tel'] ?>，客服与您核对相关信息，核实无误后会为您解除锁定。</p>
                        </div>
                </div>
        </div>
</div>