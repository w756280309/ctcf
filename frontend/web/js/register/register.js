$("input").bind('keypress', function(e) {
    if (e.keyCode === 13) {
        $('#submit').click();
    }
});

$(function() {
    $("#captchaform-captchacode-image").addClass('verity-img');

    $('.verity-ins').hover(function() {
        $('.verity-ins').css({background:'#f41c11'});
    }, function() {
        $('.verity-ins').css({background:'#f44336'});
    });

    $('.resign-btn').hover(function() {
        $('.resign-btn').css({background:'#f41c11'});
    }, function() {
        $('.resign-btn').css({background:'#f44336'});
    });

    var check = $('.agree').attr('checked');
    $('.agree').bind('click', function () {
        if (check === 'checked') {
            $('.agree').attr('checked', false);
            check = false;
        } else {
            $('.agree').attr('checked', true);
            check = 'checked';
        }
    });

    $('#submit').click(function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        signup();
    });

    //60秒倒计时
    var InterValObj; //timer变量，控制时间
    var curCount;//当前剩余秒数
    var count = 60; //间隔函数，1秒执行

    $('.verity-ins').on('click', function () {
        if($(this).hasClass('grayBack')) {
            return false;
        }

        //正常接入接口后,在表单验证通过后,调用发送短信api,在api成功callback里再调用 fun_timedown
        if(validateFormForSms()) {
            createSmscode(function () {
                fun_timedown();
            });
        }
    });

    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $('.verity-ins').removeClass('grayBack');
            $(".verity-ins").html("重新发送");
        } else {
            curCount--;
            $(".verity-ins").html(curCount + "s");
        }
    }

    function fun_timedown() {
        curCount = count;
        $('.verity-ins').addClass('grayBack');
        $(".verity-ins").html(curCount + "s");
        InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
    }
});

function validateFormForSms()
{
    if ($('#phone').val() === '') {
        $('#phone').addClass('error-border');
        $('.phone-box .popUp').html('手机号不能为空');
        return false;
    } else {
        $('#phone').removeClass('error-border');
        $('.phone-box .popUp').html('');
    }

    var tel = $('#phone').val();
    reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
    if (!reg.test(tel)) {
        $('#phone').addClass('error-border');
        $('.phone-box .popUp').html('手机号格式错误');
        return false;
    } else {
        $('#phone').removeClass('error-border');
        $('.phone-box .popUp').html('');
    }

    if ($("#verity").val() === '') {
        $('#verity').addClass('error-border');
        $('.verity-box .popUp').html('图形验证码不能为空');
        return false;
    } else {
        $('#verity').removeClass('error-border');
        $('.verity-box .popUp').html('');
    }

    if ($("#verity").val().length !== 4) {
        $('#verity').addClass('error-border');
        $('.verity-box .popUp').html('图形验证码必须为4位字符');
        return false;
    } else {
        $('#verity').removeClass('error-border');
        $('.verity-box .popUp').html('');
    }

    return true;
}

function validateForm()
{
    if (!validateFormForSms()) {
        return false;
    }

    if ($('#sms').val() === '') {
        $('#sms').addClass('error-border');
        $('.ins-box .popUp').html('手机验证码不能为空');
        return false;
    } else {
        $('#sms').removeClass('error-border');
        $('.ins-box .popUp').html('');
    }

    if ($('#sms').val().length !== 6) {
        $('#sms').addClass('error-border');
        $('.ins-box .popUp').html('手机验证码必须为6位字符');
        return false;
    } else {
        $('#sms').removeClass('error-border');
        $('.ins-box .popUp').html('');
    }

    if ($('#password').val() === '') {
        $('#password').addClass('error-border');
        $('.password-box .popUp').html('密码不能为空');
        return false;
    } else {
        $('#password').removeClass('error-border');
        $('.password-box .popUp').html('');
    }

    if ($('#password').val().length < 6) {
        $('#password').addClass('error-border');
        $('.password-box .popUp').html('密码长度最少6位');
        return false;
    } else {
        $('#password').removeClass('error-border');
        $('.password-box .popUp').html('');
    }

    var reg = /[a-zA-Z]/;
    var reg2 = /[0-9]/;
    if (!(-1 === $('#password').val().indexOf(' ') && reg.test($('#password').val()) && reg2.test($('#password').val()))) {
        $('#password').addClass('error-border');
        $('.password-box .popUp').html('请至少输入字母与数字组合');
        return false;
    } else {
        $('#password').removeClass('error-border');
        $('.password-box .popUp').html('');
    }

    if ($('.agree').attr('checked') !== 'checked') {
        $('.xieyi-err').show();
        return false;
    } else {
        $('.xieyi-err').hide();
    }

    return true;
}

function createSmscode(fun)
{
    var type = $("#sms-type").val();
    var phone = $("#phone").val();
    var captchaCode = $("#verity").val();
    var csrf = $("#csrf").val();

    $.post("/site/create-sms", {type: type, phone: phone, captchaCode: captchaCode, _csrf: csrf}, function (result) {
        if (0 === result.code) {
            fun();
        } else {
            if ('undefined' !== typeof result.key) {
                $('#'+result.key).addClass('error-border');
                $('.'+result.key+'-err').html(result.message);
            } else {
                $('#verity').addClass('error-border');
                $('.verity-box .popUp').html(result.message);
            }

            $("#captchaform-captchacode-image").click();
        }
    });
}

function signup()
{
    var $form = $('#form');
    $('.resign-btn').attr('disabled', true);

    var xhr = $.post(
        $form.attr('action'),
        $form.serialize()
    );

    xhr.done(function(data) {
        if (!data.code) {
            if ('undefined' !== typeof _paq) {
                _paq.push(['trackEvent', 'user', 'reg']);
            }

            if ('undefined' !== typeof ga) {
                ga('send', {
                    hitType: 'event',
                    eventCategory: 'reg',
                    eventAction: 'pc',
                    hitCallback: function() {
                        location.href = data.tourl;
                    }
                });
                setTimeout(function() {
                    location.href = data.tourl;
                }, 1500);
            } else {
                location.href = data.tourl;
            }
        } else {
            $.each(data.error, function(i, item) {
                var err = i + '-err';
                $('#'+i).addClass('error-border');
                $('.'+err).html(item);
            });
        }

        $('.resign-btn').attr('disabled', false);
    });

    xhr.fail(function() {
        $('.resign-btn').attr('disabled', false);
    });
}
