<?php

use frontend\assets\FrontAsset;

$this->title = '湖北日报新媒体集团旗下平台';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/luodiye/luodiye.css?v=1608015', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/JPlaceholder.js', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/register/register.js?v=20180118', ['depends' => FrontAsset::class]);

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>

<div class="container-fluid">
    <div class="banner-top"></div>
    <div class="banner-bottom"></div>
    <div class="container-content content-register">
        <?php $form = ActiveForm::begin(['action' => "/site/signup", 'id' => 'form']); ?>
        <input type="hidden" id="sms-type" value="1">
        <input type="hidden" id="csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input name="regContext" type="hidden" value="pc_landing">
        <div class="register-en">
            <h2>免费注册</h2>
            <div class="text-single phone-box">
                <div class="text-border">
                    <input id="phone" class="text-entry" type="tel" maxlength="11" name="SignupForm[phone]" placeholder="请输入手机号码" tabindex="1">
                </div>
                <p class="tip-error popUp phone-err"></p>
            </div>
            <div class="text-single varify-single verity-box">
                <div class="text-border">
                    <input id="verity" class="text-entry" type="text" name="varify" placeholder="请输入图形验证码" maxlength="4" tabindex="2" autocomplete="off">
                </div>
                <?= $form->field($captcha, 'captchaCode')->label(false)->widget(Captcha::className(), ['template' => '{image}', 'captchaAction' => '/site/captcha']) ?>
                <p class="tip-error popUp"></p>
            </div>
            <div class="text-single phonecode-single ins-box">
                <div class="text-border">
                    <input class="text-entry" id="sms" name="SignupForm[sms]" maxlength="6" type="text" name="phonecode" placeholder="请输入短信验证码" tabindex="3">
                </div>
                <a class="get-phonecode verity-ins">获取验证码</a>
                <div class="clear"></div>
                <p class="tip-error popUp sms-err"></p>
            </div>
            <div class="text-single password-box">
                <div class="text-border">
                    <?php if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) { ?>
                        <input class="text-entry" id="password" name="SignupForm[password]" maxlength="16" type="password" placeholder="请输入6到16位的密码" AUTOCOMPLETE="off">
                    <?php } else { ?>
                        <input class="text-entry" id="password" name="SignupForm[password]" maxlength="16" onfocus="this.type='password'" placeholder="请输入6到16位的密码" AUTOCOMPLETE="off">
                    <?php } ?>
                </div>
                <p class="tip-error popUp password-err"></p>
            </div>
            <div class="agreement">
                <input type="checkbox" id="agreement-button" tabindex="5" class="agree"  checked="checked">
                <label class="agreement" for="agreement-button">我已阅读并同意<a href="/site/xieyi" target="_blank">《网站服务协议》</a></label>
            </div>
            <div class="popUp xieyi-err">您未同意网站服务协议</div>
            <input type="button" class="resign-btn" id="submit" value="立即注册" tabindex="6">
            <p class="leave-login">已有账号？<a href="/site/login" target="_self">登录</a></p>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="container-fluid">
    <div class="container-content content-why">
        <h3>什么是楚天财富？</h3>
        <p>楚天财富（武汉）金融服务有限公司简称楚天财富，是由湖北日报新媒体集团发起设立的、具有国资背景的、专业从事财富管理平台和互联网金融服务平台，以专业的风控能力、丰富的产品、强大的网络技术能力，提供安全、便捷、可靠的网上服务。</p>
    </div>
</div>
<!--<div class="container-fluid container-fluid-because">-->
<!--    <div class="container-content content-because">-->
<!--        <h3>精品理财</h3>-->
<!--        <div class="content-because-border">-->
<!--            <span></span>-->
<!--        </div>-->
<!--        <div class="because-center">-->
<!--            <div class="because-left">-->
<!--                <p class="because-header">楚盈金</p>-->
<!--                <p class="because-yuxuan">预期年化利</p>-->
<!--                <p class="because-number">5.5<span>%</span>-6.5<span>%</span></p>-->
<!--                <P class="because-time"><span>—</span><span class="time-font">出借期限1-12个月</span><span>—</span></P>-->
<!--                <div class="tip_pop">-->
<!--                    <div class="tip_pop-en">-->
<!--                        <div class="tip_pop-border">-->
<!--                        </div>-->
<!--                        <div class="tip_pop-content">1000元起借</div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <a href="/licai/">立即赚钱</a>-->
<!--            </div>-->
<!--            <div class="because-right">-->
<!--                <p class="because-header">楚盈宝</p>-->
<!--                <p class="because-yuxuan">预期年化利</p>-->
<!--                <p class="because-number">6.5<span>%</span>-9<span>%</span></p>-->
<!--                <P class="because-time"><span>—</span><span class="time-font">出借期限1-24个月</span><span>—</span></P>-->
<!--                <P class="because-limit"></P>-->
<!--                <div class="tip_pop">-->
<!--                    <div class="tip_pop-en">-->
<!--                        <div class="tip_pop-border">-->
<!--                        </div>-->
<!--                        <div class="tip_pop-content">1万元起借</div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <a href="/licai/">立即赚钱</a>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div class="container-fluid">
    <div class="container-content content-because choose-width">
        <h3>为什么选择楚天财富？</h3>
        <div class="content-because-border">
            <span></span>
        </div>
        <div class="content-choose">
            <div class="choose-single choose-first">
                <div class="single-back"></div>
                <p>收益稳健，</p>
                <p>预期年化收益5.5~9%</p>
            </div>
            <div class="choose-single choose-second">
                <div class="single-back"></div>
                <p>门槛较低，</p>
                <p>1000元即可出借</p>
            </div>

            <div class="choose-single choose-third">
                <div class="single-back"></div>
                <p>资金安全，第三方</p>
                <p>资金托管平台全程监管</p>
            </div>

            <div class="choose-single choose-fourth">
                <div class="single-back"></div>
                <p>产品优质，标的小额</p>
                <p>分散，期限灵活收益稳定</p>
            </div>
        </div>
        <a class="link-last" href="/licai/">立即赚钱</a><br>
        <center><p>产品有风险，出借须谨慎</p></center>
    </div>
</div>