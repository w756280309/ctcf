<?php

use wap\assets\WapAsset;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '温州报业传媒旗下理财平台';
$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$inApp = defined('IN_APP');

$this->registerCssFile(ASSETS_BASE_URI.'css/first.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/luodiye/luodiye.css?v=161117-1', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => WapAsset::class]);
$this->registerJsFile(FE_BASE_URI.'wap/luodiye/js/luodiye.js?v=20170413-v', ['depends' => WapAsset::class]);

?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <header class="row head-title head-title1">
                <div class="logo logo1 col-xs-12 col-sm-12"><a href="/?_mark=1612"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/logo-new.png" alt="logo"></a></div>
                <div class="logo_tit logo_tit1">温州报业传媒旗下理财平台</div>
            </header>
            <div class="row banner-box">
                <div class="col-xs-12">
                    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/banner.png" alt="">
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
                    <input id="captchaform-captchacode" class="login-info text-single" type="text" name="SignupForm[captchaCode]" maxlength="4" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
                    <?= $form->field($captcha, 'captchaCode', ['template' => '{input}'])->label(false)->widget(Captcha::className(), ['template' => '{image}', 'imageOptions' => ['class' => 'varify-img'], 'captchaAction' => '/site/captcha']) ?>
                    <div class="clear"></div>
                </div>
                <div class="text-box">
                    <input id="yanzhengma" class="login-info text-single" name="SignupForm[sms]" maxlength="6" type="tel" placeholder="请输入短信验证码" AUTOCOMPLETE="off">
                    <input id="yzm" class="yzm yzm-normal get-phonecode" name="yzm" value="获取验证码" type="button">
                    <div class="clear"></div>
                </div>
                <div class="text-box password-box">
                    <input id="pass" class="login-info text-single" name="SignupForm[password]" maxlength="16" type="password" placeholder="请输入6到16位的密码" AUTOCOMPLETE="off">
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
                <p class="description-header"><span>什么是温都金服？</span></p>
                <p class="description-content">温州温都金融信息服务股份有限公司简称“温都金服”，隶属温州报业传媒旗下的理财平台。甄选各类金融机构、优质企业理财产品。提供银行级理财服务，保障用户资金，安享稳健收益。</p>
            </div>
            <div class="row production-box">
                <p class="production-header">精品理财</p>
                <div class="licai-img">
                    <div class="col-xs-6 licai-img">
                        <a href="/deal/deal/index/">
                            <img src="<?= ASSETS_BASE_URI ?>images/luodiye/prod-left.png" alt="温盈金">
                        </a>
                    </div>
                    <div class="col-xs-6 licai-img">
                        <a href="/deal/deal/index/">
                            <img src="<?= ASSETS_BASE_URI ?>images/luodiye/prod-right.png" alt="温盈宝">
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="row choose-box">
                <h3>为什么选择温都金服？</h3>
                <div class="choose-content">
                    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/choose-top.png" alt="">
                    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/why-wdjf-new.png" alt="">
                </div>
            </div>
            <a class="link-last" href="/deal/deal/index/">立即认购</a>
            <p class="danger-tip">理财非存款，产品有风险，投资须谨慎</p>
            <div class="fixed-float">
                <img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt="">
            </div>

            <?php if (!$inApp) { ?>
                <div id="wrapper">
                    <div class="fixed-box">
                        <div class="fixed-outside">
                            <div class="fixed-opacity"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt=""></div>
                            <table class="fixed-content">
                                <tr>
                                    <td colspan="3" class="table-img"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt=""></td>
                                </tr>
                                <tr class="table-content">
                                    <td width="600"><p class="content-font">使用APP客户端，理财随时随地！</p></td>
                                    <td width="300"><a class="content-link" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf" target="_self">立即下载</a></td>
                                    <td width="200">
                                        <a class="content-picture"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/close-icon-height.png" alt=""></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php if (!$inApp) { ?>
<script>
    $(function () {
        function parseUA()
        {
            var ugent = navigator.userAgent;
            var ugentS = ugent.toLowerCase(); //转换成小写
            return{
                webKit: ugent.indexOf('AppleWebKit') > -1,//苹果、谷歌内核
                mobile: !!ugent.match(/AppleWebKit.*Mobile.*/),//是否为移动终端
                ios: !!ugent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),//ios终端
                weixin: ugent.indexOf('MicroMessenger') > -1,//是否微信
                android: ugent.indexOf('Android') > -1|| ugent.indexOf('Linux') > -1,//android终端或者uc浏览器
                iPhone: ugent.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
            }
        }

        var ua = parseUA();
        function showfix()
        {
            if (ua.iPhone) {
                $(".fixed-box").show();
                $(".fixed-float").show();
            }else if(ua.android){
                $(".fixed-box").show();
                $(".fixed-float").show();
            }
        }

        function hidefix()
        {
            if (ua.iPhone) {
                $(".fixed-box").hide();
                $(".fixed-float").hide();
            }else if(ua.android){
                $(".fixed-box").hide();
                $(".fixed-float").hide();
            }
        }

        $("input.text-single").on("blur",showfix);
        $("input.text-single").on("focus",hidefix);
    });
</script>
<?php } ?>
