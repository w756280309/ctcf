function validateForm()
{
    var phone = $('#iphone').val();

    if ('' === phone) {
        return '手机号不能为空';
    }

    var reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
    if (!reg.test(phone)) {
        return '手机号格式错误';
    }

    var captchacode = $('#captchaform-captchacode').val();

    if ('' === captchacode) {
        return '图形验证码不能为空';
    }

    if (4 !== captchacode.length) {
        return '图形验证码必须为4位字符';
    }

    var yzm = $('#yanzhengma').val();

    if ('' === yzm) {
        return '短信验证码不能为空';
    }

    if (6 !== yzm.length) {
        return '手机验证码必须为6位字符';
    }

    var pass = $('#pass').val();

    if ('' === pass) {
        return '密码不能为空';
    }

    if (pass.length < 6) {
        return '密码长度最少6位';
    }

    var reg = /[a-zA-Z]/;
    var reg2 = /[0-9]/;
    if (!(-1 === pass.indexOf(' ') && reg.test(pass) && reg2.test(pass))) {
        return '请至少输入字母与数字组合';
    }

    if ('checked' !== $('#xieyi').attr('checked')) {
        return '请查看网站服务协议';
    }

    return '';
}

function signup()
{
    var $form = $('#signup_form');

    var xhr = $.post(
        $form.attr('action'),
        $form.serialize()
    );

    xhr.done(function (data) {
        $('#signup-btn').attr('disabled', false);
        if (data.code) {
            if ('undefined' !== typeof data.tourl) {
                toastCenter(data.message, function () {
                        location.href = data.tourl;
                });
            } else if ('undefined' !== typeof data.message) {
                toastCenter(data.message);
            }
        }
    });

    xhr.fail(function () {
        toastCenter('网络繁忙, 请稍后重试!');
        $('#signup-btn').attr('disabled', false);
    });
}

$(function () {
    FastClick.attach(document.body);

    $('.field-captchaform-captchacode').css('float', 'left');
    $('.content-picture').on('click', function () {
        $(this).parents('.fixed-box').hide().siblings('.fixed-float').hide();
    });

    $('input.login-info').focus(function () {
        $(this).css('color', '#000');
    });

    $('input.login-info').blur(function () {
        $(this).css('color', '');
    });

    /* 提交表单 */
    $('#signup_form').submit(function (e) {
        e.preventDefault();
        $('#signup-btn').attr('disabled', true);

        var msg = validateForm();
        if ('' !== msg) {
            toastCenter(msg, function () {
                $('#signup-btn').attr('disabled', false);
            });
            return false;
        }

        signup();
    });

    $('.password img').on('click', function () {
        if ($('#pass').attr('type') === 'password') {
            $('#pass').attr('type', 'text');
            $(this).removeAttr('src', '/images/eye-close.png');
            $(this).attr({src: '/images/eye-open.png', alt: 'eye-open'});
        } else {
            $('#pass').attr('type', 'password');
            $(this).removeAttr('src', '/images/eye-open.png');
            $(this).attr({src: '/images/eye-close.png', alt: 'eye-close'});
        }
    });

    $('#xieyi').bind('click', function () {
        if ($(this).attr('checked') === 'checked') {
            $(this).attr('checked', false);
        } else {
            $(this).attr('checked', true);
        }
    });

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

        createSms('#iphone', 1, '#captchaform-captchacode', function () {
            fun_timedown();
        });
    });

    function SetRemainTime()
    {
        if (curCount === 0) {
            window.clearInterval(InterValObj);//停止计时器
            $('#yzm').removeAttr('disabled');//启用按钮
            $('#yzm').removeClass('yzm-disabled');
            $('#yzm').val('重新发送');
        } else {
            $('#yzm').addClass('yzm-disabled');
            $('#yzm').attr('disabled', 'true');
            curCount--;
            $('#yzm').val(curCount + 's后重发');
        }
    }

    function fun_timedown()
    {
        curCount = count;
        $('#yzm').addClass('yzm-disabled');
        $('#yzm').attr('disabled', 'true');
        $('#yzm').val(curCount + 's后重发');
        InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
    }
});