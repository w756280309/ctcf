//除法函数，用来得到精确的除法结果
//说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
//调用：accDiv(arg1,arg2)
//返回值：arg1除以arg2的精确结果

function accDiv(arg1, arg2) {
    var t1 = 0, t2 = 0, r1, r2;
    try {
        t1 = arg1.toString().split(".")[1].length
    } catch (e) {
    }
    try {
        t2 = arg2.toString().split(".")[1].length
    } catch (e) {
    }
    with (Math) {
        r1 = Number(arg1.toString().replace(".", ""))
        r2 = Number(arg2.toString().replace(".", ""))
        return (r1 / r2) * pow(10, t2 - t1);
    }
}
//给Number类型增加一个div方法，调用起来更加方便。
Number.prototype.div = function (arg) {
    return accDiv(this, arg);
}


//乘法函数，用来得到精确的乘法结果
//说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
//调用：accMul(arg1,arg2)
//返回值：arg1乘以arg2的精确结果

function accMul(arg1, arg2)
{
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length
    } catch (e) {
    }
    try {
        m += s2.split(".")[1].length
    } catch (e) {
    }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
}
//给Number类型增加一个mul方法，调用起来更加方便。
Number.prototype.mul = function (arg) {
    return accMul(arg, this);
}


//加法函数，用来得到精确的加法结果
//说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
//调用：accAdd(arg1,arg2)
//返回值：arg1加上arg2的精确结果

function accAdd(arg1, arg2) {
    var r1, r2, m;
    try {
        r1 = arg1.toString().split(".")[1].length
    } catch (e) {
        r1 = 0
    }
    try {
        r2 = arg2.toString().split(".")[1].length
    } catch (e) {
        r2 = 0
    }
    m = Math.pow(10, Math.max(r1, r2))
    return (arg1 * m + arg2 * m) / m
}
//给Number类型增加一个add方法，调用起来更加方便。
Number.prototype.add = function (arg) {
    return accAdd(arg, this);
}

/**
 *减法函数
 */
function accSub(arg1, arg2)
{
    var r1, r2, m, n;
    try {
        r1 = arg1.toString().split(".")[1].length;
    } catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    } catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2));
    n = (r1 >= r2) ? r1 : r2;
    return ((arg1 * m - arg2 * m) / m).toFixed(n);
}

/**
 * 取模运算
 */
function accMod(arg1, arg2) {
    var r1, r2, m, n;
    try {
        r1 = arg1.toString().split(".")[1].length;
    } catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    } catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2));
    n = (r1 >= r2) ? r1 : r2;
    var r = (((arg1 * m) % (arg2 * m)) / m).toFixed(n);
    return r > 0 ? r : "0";
}


function subForm(form, button, falsed, datas)
{
    //console.log(datas);
    var $btn = $(button);
    if (datas == undefined) {
        var vals = $(form).serialize();
    } else {
        var vals = datas;
    }
    var to = $(form).attr("data-to");//设置如果返回错误，是否需要跳转界面

    $btn.attr('disabled', true);
    $btn.removeClass("btn-normal").addClass("btn-press");
    var xhr = $.post($(form).attr("action"), vals , function (data) {
        if (data.code == '-1') {
            alertTrue(function () {
                location.href = '/user/user';
            })
        } else if (data.code != 0 && to == 1 && data.tourl != undefined) {
            toast(data.message, function() {
                location.href = data.tourl;
            });
        } else {
            if (data.code != 0) {
                toast(data.message);
                // if (data.code > 0 && data.requiresCaptcha) {
                //     if ($('.verify-div-box').hasClass('hide')) {
                //         $('.verify-div-box').removeClass('hide');
                //     }
                //     $('#loginform-verifycode-image').attr('src', '/site/captcha?' + Math.random());
                // }
            }
            if (to == 1 && data.tourl != undefined) {
                location.href = data.tourl;
            }
        }

    });
    xhr.always(function () {
        $btn.removeClass("btn-press").addClass("btn-normal");
        $btn.attr('disabled', false);
    });
}

//*********************************************************************************

//没有遮罩的弹窗
//toast('您输入的卡号有误', function() {});
function toast(val, active)
{
    var $alert = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>');
    $alert.insertAfter($('form'));
    $alert.find('div').width($alert.width());
    setTimeout(function () {
        $alert.fadeOut();
        setTimeout(function () {
            $alert.remove();
        }, 200);
        if (active) {
            active();
        }
    }, 2000);
}

/**
 * 居中弹窗.
 */
function toastCenter(val, active)
{
    var $alert = $('<div class="error-info" style="display: block; position: fixed;"><div>' + val + '</div></div>');
    $('body').append($alert);
    $alert.find('div').width($alert.width());
    setTimeout(function () {
        $alert.fadeOut();
        setTimeout(function () {
            $alert.remove();
        }, 200);
        if (active) {
            active();
        }
    }, 2000);
}

//只有确定按钮的弹窗
//alertTrue(function(){
//    alert('true');
//});
function alertTrue(trued) {
    var chongzhi = $('<div class="mask" style="display: block"></div><div class="bing-info show"> <div class="bing-tishi">充值失败</div> <p class="tishi-p"> 请检查当前网络是否正常</p > <div class="bind-btn"> <span class="true">确定</span> </div> </div>');
    $(chongzhi).insertAfter($('form'));
    $('.bing-info').on('click', function () {
        $(chongzhi).remove();
        trued();
    })
}

//只有确定按钮的弹窗
//alertTrueVal('你输入的密码错误',function(){
//    alert('true');
//});
function alertTrueVal(val, trued) {
    var chongzhi = $('<div class="mask" style="display:block;"></div><div class="bing-info show"> <div class="bing-tishi">温馨提示</div> <p class="tishi-p" style="line-height: 20px;"> ' + val + '，即将跳转到相应的页面完成相应的操作</p > <div class="bind-btn"> <span class="true">我知道了</span> </div> </div>');
    $(chongzhi).insertAfter($('form'));
    $('.bing-info').on('click', function () {
        $(chongzhi).remove();
        if (typeof trued !== 'undefined') {
            trued();
        }
    });
}

function createSms(phoneId, type, captchaCodeId, trued, failed)
{
    var phone = $(phoneId).val();
    var captchaCode = $(captchaCodeId).val();

    if ($('#yzm').attr('disabled')) {
        return false;
    }
    $('#yzm').attr('disabled', true);

    var csrf = $("meta[name=csrf-token]").attr('content');
    var xhr = $.ajax({
        url: '/site/createsmscode',
        method: 'POST',
        timeout: 10000,
        data: {type: type, phone: phone, captchaCode: captchaCode, _csrf: csrf},
        dataType: 'json'
    });

    xhr.done(function(data) {
        $('#yzm').removeAttr('disabled');//启用按钮
        if (data.code == 0) {
            if ('undefined' !== typeof trued) {
                trued(); //执行倒计时
            }
        } else {
            toastCenter(data.message);

            if ('undefined' !== typeof failed) {
                failed(data);
            }

            $("#captchaform-captchacode-image").click();
        }
    });

    xhr.fail(function () {
        $('#yzm').removeAttr('disabled');//启用按钮
        $("#captchaform-captchacode-image").click();
        toastCenter('网络繁忙, 请稍后重试!');
    });

    xhr.always(function() {
        var yzmString = $('#yzm').val();
        if ('获取验证码' === yzmString || '重新发送' === yzmString) {
            $('#yzm').removeAttr('disabled');//启用按钮
        }
    });
}
