<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="开通快捷支付充值";
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/kaitongkuaijiezhifu.css"/>
<div class="container" style="background: #fff">
    <div class="row">
        <div class="col-xs-12 kaitong">开通须知：</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content">1，快捷卡是移动端充值，提现的唯一途径</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content">2，根据同卡进出的原则，用户只能使用唯一一张绑定的银行卡进行充值可提现</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content">3，为了保证您账户资金的安全，如需要更换或解绑快捷卡，请联系我们客服（400-888-8888），客户确认身份信息后，解除绑定</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content">4，提现的金额采取T+1到账（节假日顺延）</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content">5，充值限额说明：单笔限额5万元，每日限额50万元</div>
    </div>
    <div class="row">
        <div class="col-xs-12 kaitong-content" style="text-align: center"><img src="/images/kuaijie.png" alt=""/></div>
    </div>
    </div>
</div>
<div class="container" style="padding-bottom: 40px">
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-6 login-sign-btn">
            <input class="btn-common btn-normal" type="button" value="立即开通" onclick="location.href='/user/userbank/idcardrz'">
        </div>
        <div class="col-xs-3"></div>
    </div>
</div>