function validateForm()
{
    if ($('#iphone').val() === '') {
        return '手机号不能为空';
    }

    var tel = $('#iphone').val();
    reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
    if (!reg.test(tel)) {
        return '手机号格式错误';
    }

    if ($('#captchaform-captchacode').val() === '') {
        return '图形验证码不能为空';
    }

    if ($('#captchaform-captchacode').val().length !== 4) {
        return '图形验证码必须为4位字符';
    }

    if ($('#yanzhengma').val() === '') {
        return '短信验证码不能为空';
    }

    if ($('#yanzhengma').val().length !== 6) {
        return '手机验证码必须为6位字符';
    }

    if ($('#pass').val() === '') {
        return '密码不能为空';
    }

    if ($('#pass').val().length < 6) {
        return '密码长度最少6位';
    }

    var reg = /[a-zA-Z]/;
    var reg2 = /[0-9]/;
    if (!(-1 === $('#pass').val().indexOf(' ') && reg.test($('#pass').val()) && reg2.test($('#pass').val()))) {
        return '请至少输入字母与数字组合';
    }

    if ($('#xieyi').attr('checked') !== 'checked') {
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
        if (data.code) {
            if ('undefined' !== typeof data.tourl) {
                toastCenter(data.message, function () {
                    if ('undefined' !== typeof ga) {
                        ga('send', {
                            hitType: 'event',
                            eventCategory: 'reg',
                            eventAction: 'm',
                            hitCallback: function () {
                                location.href = data.tourl;
                            }
                        });
                        setTimeout(function () {
                            location.href = data.tourl;
                        }, 1500);
                    } else {
                        location.href = data.tourl;
                    }
                });
            } else if ('undefined' !== typeof data.message) {
                toastCenter(data.message);
            }
        }
        $('#signup-btn').attr('disabled', false);
    });

    xhr.fail(function () {
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

        var mess = validateForm();
        if ('' !== mess) {
            toastCenter(mess, function () {
                $('#signup-btn').attr('disabled', false);
            });

            return false;
        }

        signup();
    });

    $('.login-eye img').on('click', function () {
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
        if ($('#captchaform-captchacode').val() === '') {
            toastCenter('图形验证码不能为空');
            return false;
        }
        if ($('#captchaform-captchacode').val().length !== 4) {
            toastCenter('图形验证码必须为4位字符');
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