<?php

use common\utils\StringUtils;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '安全中心';

foreach (['useraccount/safecenter.css'] as $cssFile) {
    $this->registerCssFile(ASSETS_BASE_URI.'css/'.$cssFile, ['depends' => 'frontend\assets\FrontAsset']);
}

?>

<div class="safecenter-box">
    <div class="safecenter-header">
        <div class="safecenter-header-icon"></div>
        <span class="safecenter-header-font">安全中心</span>
    </div>

    <div class="safecenter-content">
        <p class="safecenter-tel">您好，<?= StringUtils::obfsMobileNumber($user->mobile) ?></p>
        <!--  实名认证 -->
        <div class="safecenter-content-info">
            <div class="info-box">
                <div class="info info-icon info-id">
                    <i></i>
                </div>
                <div class="info info-name">
                    <h4>实名认证</h4>
                    <p>为了您的账户安全，请及时进行身份认证。</p>
                </div>
                <div class="info info-person">
                    <!-- 未认证-->
                    <?php if ($user->isIdVerified()): ?><p><?= $user->real_name ?>&nbsp;&nbsp;身份证号：<?= substr($user->idcard, 0, 4) ?>****&nbsp;&nbsp;****<?= substr($user->idcard, -4, 4) ?></p><?php endif; ?>
                </div>
                <div class="info-btn">
                    <!-- 未认证-->
                    <?php if ($user->isIdVerified()): ?>
                    <i class="blue"></i><span>已认证</span>
                    <?php else: ?>
                    <i class="red"></i><span>未认证</span>
                    <i class=""></i><a href="/user/userbank/idcardrz" class="bunding renzheng">立即认证</a>
                    <?php endif; ?>
                    <!-- 已认证-->
                </div>
            </div>

        </div>

        <!--  手机号绑定 -->
        <div class="safecenter-content-info">
            <div class="info-box">
                <div class="info info-icon info-phone">
                    <i></i>
                </div>
                <div class="info info-name">
                    <h4>手机号绑定</h4>
                    <p>账户资金变动，活动信息及时通知。</p>
                </div>
                <div class="info info-person">
                    <!-- 已认证-->
                    <p><?= StringUtils::obfsMobileNumber($user->mobile) ?></p>
                </div>
                <div class="info-btn">
                    <!-- 已认证-->
                    <i class="blue"></i><span>已绑定</span>
                </div>
            </div>
        </div>

        <!--  银行卡绑定 -->
        <div class="safecenter-content-info">
            <div class="info-box">
                <div class="info info-icon info-card">
                    <i></i>
                </div>
                <div class="info info-name">
                    <h4>银行卡绑定</h4>
                    <p>绑定银行卡，理财更任性。</p>
                </div>
                <?php if (null !== $user->qpay): ?>
                <div class="info info-person">
                    <p><?= substr($user->qpay->card_number, 0, 4) ?>****&nbsp;&nbsp;****<?= substr($user->qpay->card_number, -4, 4) ?></p>
                </div>
                <?php endif; ?>
                <div class="info-btn">
                    <!-- 未认证-->
                    <?php if (null === $user->qpay): ?>
                    <i class="red"></i><span>未绑定</span>
                    <i class="/user/userbank/bindbank"></i><a href="/user/userbank/bindbank" class="bunding">绑定</a>
                    <?php else: ?>
                    <!-- 已认证-->
                    <i class="blue"></i><span>已绑定</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!--  登录密码 -->
        <div class="safecenter-content-info revise">
            <div class="revise-info-box">
                <div class="info info-icon info-login-code">
                    <i></i>
                </div>
                <div class="info info-name">
                    <h4>登录密码</h4>
                    <p>为了您的账户安全，请您定期更换登录密码。</p>
                </div>
                <div class="info-btn">
                    <!-- 已设置-->
                    <i class="blue"></i><span>已设置</span>
                    <i class=""></i><a href="javascript:;" class="join">修改</a>
               </div>
            </div>
            <div class="revise-container hide">
                <?php $form = ActiveForm::begin(['action' => "/user/securitycenter/reset-pass", 'id' => 'form']); ?>
                <div class="revise-login-code">
                    <div class="revise-login-bag">
                        <span>原密码</span><input type="password" id="password" class="input-code" name="EditpassForm[password]" placeholder="请输入原密码" autocomplete="off">
                    </div>
                    <p class="error password_err"></p>
                    <div class="revise-login-bag">
                        <span>新密码</span><input type="password" id="new_pass" class="input-code" name="EditpassForm[new_pass]" placeholder="请输入6-20位的新密码" maxlength="20" autocomplete="off">
                    </div>
                    <p class="error new_pass_err"></p>
                    <div class="revise-login-bag">
                        <span class="yzm-txt">验证码</span><input type="text" id="verifyCode" class="input-yzm-code" name="EditpassForm[verifyCode]" placeholder="请输入图形验证码" autocomplete="off" maxlength="4">
                        <?=
                            $form->field($model, 'verifyCode')->label(false)->widget(Captcha::className(), [
                                'template' => '{image}', 'captchaAction' => '/site/captcha',
                            ])
                        ?>
                    </div>
                    <p class="error verifyCode_err"></p>
                    <input type="button" class="a-submit" id="submit" value="确认修改">
                </div>
                <?php ActiveForm::end(); ?>
            </div>
       </div>

       <!--  支付密码 -->
        <div class="safecenter-content-info pay">
            <div class="revise-info-box">
                <div class="info info-icon info-pay-code">
                    <i></i>
                </div>
                <div class="info info-name">
                    <h4>支付密码</h4>
                    <p>为了您的账户安全，请您定期更换支付密码。</p>
                </div>
                <div class="info-btn">
                    <!-- 已设置-->
                    <?php if ($user->isIdVerified()): ?>
                    <i class="blue"></i><span>已设置</span>
                    <i class=""></i><a href="javascript:;" class="join">修改</a>
                    <?php else: ?>
                    <i class="red"></i><span>未设置</span>
                    <i class=""></i><a href="/user/userbank/idcardrz" class="bunding">开户</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="revise-container hide">
                <div class="pay-code">
                    <div class="pay-bag">
                        <p class="p-bloder">忘记资金托管账户支付密码</p>
                        <p>若您忘记了您的资金托管账户支付密码，可以申请密码重置，联动优势将新的支付密码以短信形式发送到您的手机，请注意接收并妥善保管。</p>
                    </div>
                    <a id="x-reset-ump-pass" class="pay-submit" href="" >重置支付密码</a>
                    <div style="clear: both"></div>

                    <div class="pay-txt">
                        <p class="p-bloder">修改资金托管账户支付密码</p>
                        <p>您可以编辑短信 “<span class="p-tip"> GGMM # 原密码 # 新密码 </span>” (例如:GGMM#123456#234567) 发送至优势修改您的支付密码，支付密码只能是6位数字。</p>
                        <p>联动优势短信号码:移动，联通，电信用户编辑短信发至10690569687</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("input").bind('keypress', function(e) {
        if (e.keyCode === 13) {
            $('#submit').click();
        }
    });

    $(function() {
        //张开收缩修改密码
        var c=$('.revise .info-btn .join').eq(0);
        var d=$('.revise .revise-container').eq(0);
        slowOpen(c,d);

        var e=$('.pay .info-btn .join').eq(0);
        var f=$('.pay .revise-container').eq(0);
        slowOpen(e,f);

        function slowOpen(a,b){
            $(a).on('click',function(){
                var index=$(a).index(this);
                $(b).stop(true,false).eq(index).slideToggle();
                $("#editpassform-verifycode-image").addClass('yzm');
            });
        }

        var csrf = '<?= Yii::$app->request->csrfToken?>';
        var allowSub = true;
        $('#x-reset-ump-pass').on('click', function(e) {
            e.preventDefault();

            if (!allowSub) {
                return;
            }

            allowSub = false;
            var xhr = $.ajax({
                type: 'POST',
                url: '/user/securitycenter/reset-ump-pass',
                data: {'_csrf': csrf},
                dataType: 'json'
            });

            xhr.done(function(data) {
                alert(data.message);
                allowSub = true;
            });

            xhr.fail(function(jqXHR) {
                alert('请求失败');
                allowSub = true;
            });
        });

        $('#submit').click(function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return false;
            }

            subForm();
        });
    });

    function validateForm()
    {
        if ($('#password').val() === '') {
            $('#password').addClass('error-border');
            $('.password_err').html('原密码不能为空');
            return false;
        } else {
            $('#password').removeClass('error-border');
            $('.password_err').html('');
        }

        if ($('#new_pass').val() === '') {
            $('#new_pass').addClass('error-border');
            $('.new_pass_err').html('新密码不能为空');
            return false;
        } else {
            $('#new_pass').removeClass('error-border');
            $('.new_pass_err').html('');
        }

        if ($('#new_pass').val().length < 6) {
            $('#new_pass').addClass('error-border');
            $('.new_pass_err').html('密码长度最少6位');
            return false;
        } else {
            $('#new_pass').removeClass('error-border');
            $('.new_pass_err').html('');
        }

        var reg = /[a-zA-Z]/;
        var reg2 = /[0-9]/;
        if (!(-1 === $('#new_pass').val().indexOf(' ') && reg.test($('#new_pass').val()) && reg2.test($('#new_pass').val()))) {
            $('#new_pass').addClass('error-border');
            $('.new_pass_err').html('请至少输入字母与数字组合');
            return false;
        } else {
            $('#new_pass').removeClass('error-border');
            $('.new_pass_err').html('');
        }

        return true;
    }

    function subForm()
    {
        var $form = $('#form');
        $('.a-submit').attr('disabled', true);

        var xhr = $.post(
            $form.attr('action'),
            $form.serialize()
        );

        xhr.done(function(data) {
            $('.a-submit').attr('disabled', false);

            if (data.code) {
                $.each(data.error, function(i, item) {
                    var err = i + '_err';
                    $('#'+i).addClass('error-border');
                    $('.'+err).html(item);
                });

                $("#editpassform-verifycode-image").click();
            } else {
                if ('undefined' !== typeof data.tourl) {
                    location.href = data.tourl;
                }
            }
        });

        xhr.fail(function() {
            $('.a-submit').attr('disabled', false);
            $("#editpassform-verifycode-image").click();
        });
    }
</script>
