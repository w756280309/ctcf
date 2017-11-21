<?php

$this->title = '温都金服';

use yii\web\JqueryAsset;
$this->registerJsFile(FE_BASE_URI . 'res/js/js.cookie.js', ['depends' => JqueryAsset::class]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/20170726luodiye/css/index.css">
<script src="<?= ASSETS_BASE_URI ?>js/common.js?v=20170920"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery.lazyload.min.js"></script>

<div class="flex-content">
    <div class="banner">
        <?php if (Yii::$app->controller->action->getUniqueId() === 'luodiye/cloth') { ?>
            <img src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/banner-cloth.png" alt="">
        <?php } else { ?>
            <img src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/banner.png" alt="">
        <?php } ?>
        <img onclick='location.href="/?_mark=<?= time() ?>"' src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/home.png" alt="">
    </div>
    <div class="content-box">
        <!--form表单-->
        <div class="form-action">
            <form action="/site/signup?next=<?= urlencode(Yii::$app->params['clientOption']['host']['wap'].'?_mark'.time()) ?>" method="post" id="signup_new_form">
            <input type="hidden" name="_csrf" id="form_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <p class="des">欢迎加入我们</p>
                <!--点击领取-->
                <div class="step-one">
                    <input type="tel" id="iphone" name="SignupForm[phone]" placeholder="请输入您的手机号" maxlength="11" AUTOCOMPLETE="off" value="<?= $phone ?>">
                    <div class="imgCode-box">
                        <input type="text" AUTOCOMPLETE="off" name="SignupForm[captchaCode]" id="captchaform-captchacode" placeholder="请输入图形验证码" maxlength="4">
                        <div class="form-group field-captchaform-captchacode required">
                            <img id="captchaform-captchacode-image" class="get-imgCode" src="/site/captcha?v=<?= time() ?>" alt="">
                        </div>
                    </div>
                    <input id="yzm" type="button" class="btn" value="立即领取">
                </div>

                <!--点击立即领取后-->
                <div class="step-two" style="display: none">
                    <p class="tips">验证码已发送到此手机号</p>
                    <div class="clearfix phoneNum" id="updateMobile"><p class="lf new-mobile"></p><p class="rg">修改手机号</p></div>
                    <div class="imgCode-box">
                        <input id="sms" type="tel" class="input-common" name="SignupForm[sms]" placeholder="请输入手机验证码" maxlength="6" AUTOCOMPLETE="off">
                        <span class="get-imgCode code-special" id="yzm_refresh">获取验证码</span>
                    </div>
                    <input id="password" type="password" class="input-common" name="SignupForm[password]" maxlength="20" placeholder="请输入密码(6-20位字母和数字组合)" AUTOCOMPLETE="off">
                    <input type="button" name="sign-up-mobile" id="final-submit" class="btn" value="立即领取">
                </div>

                <div class="protocols clearfix">
                    <p class="lf overflowHidden">我已经阅读并同意<a href="/site/xieyi">《网站服务协议》</a></p>
                    <p class="rg">已有账号？<a href="/site/login">登录</a></p>
                </div>
            </form>
        </div>
        <!--介绍-->
        <div class="addwdjf">
            <p class="des">为什么加入我们-温都金服</p>
            <div class="first-reason">
                <p>收益稳健</p>
                <p>5万元存一年，预期获得收益对比</p>
                <div class="chart">
                    <img data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/chart_new.png" alt="">
                </div>
            </div>
            <div class="second-reason">
                <div class="top">
                    <img  src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/bg.png" alt="">
                    <p class="titles">股东强势 国资背景</p>
                </div>
                <div class="ctn">
                    <dl class="dl-one">
                        <dt class="dt-com">什么是温都金服？</dt>
                        <dd>温都金服是隶属温州报业传媒旗下的理财平台。甄选各类权威机构的理财产品。提供银行级理财服务，保障用户资金安全，安享稳定收益。</dd>
                    </dl>
                    <dl class="dl-one">
                        <dt class="dt-com m60">股东背景</dt>
                        <dd><img data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/contact.png" alt=""></dd>
                    </dl>
                    <dl class="dl-one">
                        <dt class="dt-com m60">平台优势</dt>
                        <dd><img data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/introduce_new.png" alt=""></dd>
                    </dl>
                </div>

            </div>
            <div class="second-reason">
                <div class="top">
                    <img  src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/bg.png" alt="">
                    <p class="titles">本地服务方便快捷</p>
                </div>
                <div class="ctn address">
                    <ul  class="clearfix">
                        <li class="lf">
                            <img data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/address_01.png" alt="">
                            <p>四楼办公区，一对一的专业理财咨询，为用户提供优质理财服务</p>
                        </li>
                        <li class="rg">
                            <img class="rg" data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/address_02.png" alt="">
                            <p class="rg">一楼服务大厅,与温州都市报共同提供用户服务窗口</p>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="second-reason">
                <div class="top">
                    <img  src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/bg.png" alt="">
                    <p class="titles">权威媒体合作支持</p>
                </div>
                <div class="ctn">
                    <img class="media" data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/media.png" alt="">
                </div>
            </div>
        </div>
        <!--底部说明-->
        <div class="bottom-ctn">
            <p class="tips">*本次活动最终解释权归温都金服所有与苹果公司无关</p>
            <ul class="clearfix">
                <li class="lf">
                    <p>400-101-5151</p>
                    <p>（8:30~20:00）</p>
                    <p class="overflowHidden">地址：温州市鹿城区飞霞南路657号保丰大楼四层</p>
                </li>
                <li class="rg"><img data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/erweima.png" alt=""></li>
            </ul>
        </div>
    </div>
</div>
<img class="footer-logo" data-original="<?= FE_BASE_URI ?>wap/20170726luodiye/images/footer-logo.png" alt="">
<img class="toTop" src="<?= FE_BASE_URI ?>wap/20170726luodiye/images/toTop.png" alt="">
<script>
    $(window).on("scroll",function(){
        if($(this).scrollTop()>300){
            $(".toTop").show();
        } else {
            $(".toTop").hide();
        }
    });
    $(".toTop").on('click',function(){
        $("body,html").animate({scrollTop: 0},1000);
    });
    $("img").lazyload({ threshold : 200 });

    $(function(){
        //60秒倒计时
        var InterValObj; //timer变量，控制时间
        var curCount;//当前剩余秒数
        var count = 60; //间隔函数，1秒执行
        $('#yzm').on('click', function () {
            msg = '';
            if ('' === $('#captchaform-captchacode').val()) {
                msg = '图形验证码不能为空';
            } else if (4 !== $('#captchaform-captchacode').val().length) {
                msg = '图形验证码必须为4位字符';
            }

            if ('' !== msg) {
                toastCenter(msg);
                return false;
            }

            if ($('#yzm').hasClass('yzm-disabled')) {
                return false;
            }
            $('#yzm').addClass('yzm-disabled');

            createSmsNew('#iphone', 1, '#captchaform-captchacode', function () {
                fun_timedown();
                $('.step-two').show();
                $('.step-one').hide();
                $('.phoneNum .new-mobile').html($('#iphone').val());
            });
        });
        $('#yzm_refresh').on('click', function () {
            if ($('#yzm_refresh').hasClass('yzm-disabled')) {
                return false;
            }
            $('#yzm_refresh').addClass('yzm-disabled');
            createSmsNew('#iphone', 1, '#captchaform-captchacode', function () {
                fun_timedown();
            });
        });
        $('#captchaform-captchacode-image').on('click', function () {
            $(this).attr('src', '/site/captcha?v='+Math.random());
        });
        $('#updateMobile').on('click', function(){
            $('#iphone').val('');
            $('#captchaform-captchacode').val('');
            $('#captchaform-captchacode-image').trigger('click');
            $('.step-two').hide();
            $('.step-two .input-common').val('');
            $('#yzm_refresh').removeClass('yzm-disabled');
            $('#yzm_refresh').html('重新发送');
            $('.step-one').show();
            $('#updateMobile .new-mobile').html('');
        });

        function SetRemainTime()
        {
            if (curCount === 0) {
                window.clearInterval(InterValObj);//停止计时器
                $('#yzm_refresh').removeClass('yzm-disabled');
                $('#yzm_refresh').html('重新发送');
            } else {
                $('#yzm_refresh').addClass('yzm-disabled');
                curCount--;
                $('#yzm_refresh').html(curCount + 's后重发');
            }
        }

        function fun_timedown()
        {
            curCount = count;
            $('#yzm_refresh').addClass('yzm-disabled');
            $('#yzm_refresh').val(curCount + 's后重发');
            InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
        }

        $('#final-submit').on('click', function(){
            if ($(this).hasClass('sub-disabled')) {
                return false;
            }
            $(this).addClass('sub-disabled');
            var csrf = $('#form_csrf').val();
            $.post($('#signup_new_form').attr('action'), {'_csrf':csrf,'phone':$('.new-mobile').html(),'sms':$('#sms').val(),'father':$('#password').val()}, function(data) {
                $('#final-submit').removeClass('sub-disabled');
                if (1 === data.code && 'undefined' !== typeof data.tourl) {
                    Cookies.set('showIndexPop', true);
                    window.setTimeout(function(){
                        location.href = data.tourl;
                    }, 500);
                } else {
                    toastCenter(data.message);
                }
            });
        });
    });

    function createSmsNew(phoneId, type, captchaCodeId, trued)
    {
        var phone = $(phoneId).val();
        var captchaCode = $(captchaCodeId).val();
        var csrf = $('#form_csrf').val();
        var xhr = $.ajax({
            url: '/luodiye/create-sms',
            method: 'POST',
            timeout: 10000,
            data: {type: type, phone: phone, captchaCode: captchaCode, _csrf: csrf},
            dataType: 'json'
        });

        xhr.done(function (data) {
            $('#yzm_refresh').removeClass('yzm-disabled');
            $('#yzm').removeClass('yzm-disabled');
            if (0 === data.code) {
                if ('undefined' !== typeof trued) {
                    trued();
                }
            } else {
                toastCenter(data.message);
                $('#captchaform-captchacode-image').trigger('click');
            }
        });

        xhr.fail(function () {
            $('#yzm').removeClass('yzm-disabled');
            $('#yzm_refresh').removeClass('yzm-disabled');
            $('#captchaform-captchacode-image').trigger('click');
            toastCenter('网络繁忙, 请稍后重试!');
        });

        xhr.always(function() {
            $('#yzm').removeClass('yzm-disabled');
            var yzmString = $('#yzm_refresh').html();
            if ('获取验证码' === yzmString || '重新发送' === yzmString) {
                $('#yzm_refresh').removeClass('yzm-disabled');
            }
        });
    }
</script>