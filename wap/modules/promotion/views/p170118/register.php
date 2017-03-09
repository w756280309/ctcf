<?php

$this->title = '温都猫';
$config = json_decode($promo->config, true);

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css?v=20170119">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170119">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/wendumao/css/register.css?v=20170119">
<script  src="<?= FE_BASE_URI ?>libs/lib.flexible3.js?v=20170119"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js?v=20170119"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js?v=20170119"></script>
<script src="<?= FE_BASE_URI ?>wap/wendumao/js/luodiye.js?v=20170309"></script>

<div class="container flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="header">
            <ul class="clearfix">
                <li class="lf f16"><img src="<?= FE_BASE_URI ?>wap/wendumao/images/logo.png" alt="">温都金服国资平台</li>
                <li class="rg f13"><a href="/">返回首页</a></li>
            </ul>
        </div>
    <?php } ?>

    <?php if (isset($config['image'])) { ?>
        <div class="banner">
            <img  src="<?= $config['image'] ?>" alt="">
        </div>
    <?php } ?>

    <div class="formcheck f15">
        <?php $next = Yii::$app->request->hostInfo.'/promotion/p170118/res'; ?>
        <?php $form = ActiveForm::begin(['id' => 'signup_form', 'action' => '/site/signup?next='.urlencode($next)]); ?>
            <input name="regContext" type="hidden" value="m_wdm1701">
            <div class="phonenum">
                <input id="iphone" name="SignupForm[phone]" type="tel" value="<?= $mobile ?>" maxlength="11" placeholder="请输入手机号">
            </div>
            <div class="piccode">
                <input id="captchaform-captchacode" name="SignupForm[captchaCode]" maxlength="4" type="text" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
                <?=
                    $form->field($captcha, 'captchaCode', [
                        'template' => '{input}',
                    ])->label(false)
                    ->widget(Captcha::className(), [
                        'template' => '{image}',
                        'imageOptions' => [
                            'class' => 'varify-img',
                            'id' => 'captchaform-captchacode-image',
                        ],
                        'captchaAction' => '/site/captcha',
                    ])
                ?>
            </div>
            <div class="phonecode">
                <input id="yanzhengma" type="tel" name="SignupForm[sms]" maxlength="6" placeholder="请输入短信验证码" AUTOCOMPLETE="off"><input type="button" value="获取验证码" id="yzm">
            </div>
            <div class="password">
                <input id="pass" type="password" name="SignupForm[password]" maxlength="20" placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
                <img alt="eye-close" src="<?= FE_BASE_URI ?>wap/wendumao/images/eye-close.png">
            </div>
            <div class="contract">
                <input type="checkbox" checked="checked" id="xieyi"> 我已经阅读并同意<a href="/site/xieyi">《网站服务协议》</a>
            </div>
            <button class="register" id="signup-btn">立即注册</button>
        <?php $form->end(); ?>
        <p class="f15">已有账号？<a href="/site/login">登录</a></p>
    </div>

    <div class="bottomside">
        <p class="f12 botintro">如有问题请拨打客服电话或关注温都金服公众号</p>
        <dl>
            <dt style="width: 70%;">
            <div class="clearfix">
                <img class="lf"  src="<?= FE_BASE_URI ?>wap/wendumao/images/icon_03.png" alt="">
                <p class="lf">
                    <a class="f15" href="tel:400-101-5151" style="line-height:0.6266667rem;">400-101-5151</a>
                    <span class="f12" style="line-height:0.56rem;">工作时间：8:30~20:00</span>
                </p>
            </div>
            <div class="f12 netaddress">官网地址：<a href="<?= defined('IN_APP') ? '/' : 'https://www.wenjf.com' ?>">www.wenjf.com</a></div>
            <div class="f12 address clearfix"><p class="lf">公司地址：</p><p class="specialp lf">温州市鹿城区飞霞南路657号保丰大楼四层</p></div>
            </dt>
            <dd class="erweima f11"><img src="<?= FE_BASE_URI ?>wap/wendumao/images/erweima.png" alt=""><p>微信公众号</p></dd>
        </dl>
        <p class="f11">*理财非存款，产品有风险，投资须谨慎</p>
    </div>
</div>

<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
