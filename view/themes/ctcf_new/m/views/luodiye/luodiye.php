<?php

use wap\assets\WapAsset;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '湖北日报新媒体集团旗下理财平台';
$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$inApp = defined('IN_APP');

$this->registerCssFile(ASSETS_BASE_URI.'css/first.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/luodiye/luodiye.css?v=161117-1', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => WapAsset::class]);
$this->registerJsFile(FE_BASE_URI.'wap/luodiye/js/luodiye.js?v=20180118', ['depends' => WapAsset::class]);

?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <header class="row head-title head-title1">
                <div class="logo logo1 col-xs-12 col-sm-12"><a href="/?_mark=1612"><img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/logo-new.png" alt="logo"></a></div>
                <div class="logo_tit logo_tit1">湖北日报新媒体集团旗下理财平台</div>
            </header>
            <div class="row banner-box">
                <div class="col-xs-12">
                    <img src="<?= ASSETS_BASE_URI ?>ctcf/images/invite/banner_new.png" alt="">
                    <div class="banner-bottom"></div>
                    <ul class="banner-bottom-wrap">
                        <li><img src="<?= ASSETS_BASE_URI ?>images/luodiye/lingxing.png"><span>国资平台</span></li>
                        <li><img src="<?= ASSETS_BASE_URI ?>images/luodiye/lingxing.png"><span>股东强势</span></li>
                        <li><img src="<?= ASSETS_BASE_URI ?>images/luodiye/lingxing.png"><span>收益稳健</span></li>
                    </ul>
                </div>
            </div>

            <?php
                $actionUrl = '/site/signup';
                if (!empty($next)) {
                    $actionUrl .= '?next='.urlencode($next);
                }
            ?>
            <?php $form = ActiveForm::begin(['id' => 'signup_form', 'action' => $actionUrl]); ?>
            <div class="row register-box">
                <div class="text-box">
                    <input name="regContext" type="hidden" value="m_intro1611">
                    <?php if (!empty($next)) { ?>
                        <input name="next" type="hidden" value="<?= urlencode($next) ?>">
                    <?php } ?>
                    <input id="iphone" name="SignupForm[phone]" class="text-single login-info" maxlength="11" type="tel" placeholder="请输入手机号">
                    <div class="clear"></div>
                </div>
                <div class="text-box">
                    <input id="captchaform-captchacode" class="login-info text-single" type="tel" name="SignupForm[captchaCode]" maxlength="4" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
                    <?= $form->field($captcha, 'captchaCode', ['template' => '{input}'])->label(false)->widget(Captcha::className(), ['template' => '{image}', 'imageOptions' => ['class' => 'varify-img'], 'captchaAction' => '/site/captcha']) ?>
                    <div class="clear"></div>
                </div>
                <div class="text-box">
                    <input id="yanzhengma" class="login-info text-single" name="SignupForm[sms]" maxlength="6" type="tel" placeholder="请输入短信验证码" AUTOCOMPLETE="off">
                    <input id="yzm" class="yzm yzm-normal get-phonecode" name="yzm" value="获取验证码" type="button">
                    <div class="clear"></div>
                </div>
                <div class="text-box password-box">
                    <input id="pass" class="login-info text-single" name="" maxlength="16" type="password" placeholder="请输入6到16位的密码" AUTOCOMPLETE="off" onfocus="this.type='password'">
                    <input id="pass2" class="login-info text-single" name="SignupForm[password]" maxlength="16" type="hidden" placeholder="请输入6到16位的密码" AUTOCOMPLETE="off">
                    <a class="eye-choose login-eye password">
                        <img width="26" height="20" alt="eye-close" src="<?= ASSETS_BASE_URI ?>images/eye-close.png">
                    </a>
                    <div class="clear"></div>
                </div>
                <div class="agreement">
                    <table>
                        <tr>
                            <td><input type="checkbox" id="xieyi" class="xieyi lf" checked="checked"></td>
                            <td>
                                <label class="agreement div-xieyi" for="xieyi">我已阅读并同意<a href="/site/xieyi" target="_blank">《网站服务协议》</a></label>
                            </td>
                        </tr>
                    </table>
                </div>
                <input type="submit" id="signup-btn" class="register-submit btn-normal" value="立即注册">
                <p class="leave-login">已有账号？<a href="/site/login" target="_self">登录</a></p>
            </div>
            <?php $form->end(); ?>

            <div class="row description-box">
                <p class="description-header"><span>什么是楚天财富？</span></p>
                <p class="description-content">楚天财富（武汉）金融服务有限公司简称楚天财富，是由湖北日报新媒体集团发起设立的、具有国资背景的、专业从事财富管理平台和互联网金融服务平台，以专业的风控能力、丰富的理财产品、强大的网络技术能力，提供安全、便捷、可靠的网上理财服务。</p>
            </div>
<!--            <div class="row production-box">-->
<!--                <p class="production-header">精品理财</p>-->
<!--                <div class="licai-img">-->
<!--                    <div class="col-xs-6 licai-img">-->
<!--                        <a href="/deal/deal/index/">-->
<!--                            <img src="--><?//= ASSETS_BASE_URI ?><!--images/luodiye/wyj.png" alt="温盈金">-->
<!--                        </a>-->
<!--                    </div>-->
<!--                    <div class="col-xs-6 licai-img">-->
<!--                        <a href="/deal/deal/index/">-->
<!--                            <img src="--><?//= ASSETS_BASE_URI ?><!--images/luodiye/wyb.png" alt="温盈宝">-->
<!--                        </a>-->
<!--                    </div>-->
<!--                    <div class="clear"></div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="row choose-box">
                <h3>为什么选择楚天财富？</h3>
                <div class="choose-content">
                    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/choose_new.png" alt="">
                    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/why-wdjf-new.png" alt="">
                </div>
            </div>
            <a class="link-last" href="/deal/deal/index/">立即认购</a>
            <p class="danger-tip">理财非存款，产品有风险，投资须谨慎</p>
            <div class="fixed-float">
                <img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt="">
            </div>
        </div>
    </div>
</div>

