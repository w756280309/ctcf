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

//在你要用的地方包含这些函数，然后调用它来计算就可以了。
//比如你要计算：7*0.8 ，则改成 (7).mul(8)
//其它运算类似，就可以得到比较精确的结果。



//减法函数
function Subtr(arg1, arg2) {
    var r1, r2, m, n;
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
    m = Math.pow(10, Math.max(r1, r2));
    //last modify by deeka
    //动态控制精度长度
    n = (r1 >= r2) ? r1 : r2;
    return ((arg1 * m - arg2 * m) / m).toFixed(n);
}

function subForm(form, button, falsed)
{
    var $btn = $(button);
    var vals = $(form).serialize();
    var to = $(form).attr("data-to");//设置如果返回错误，是否需要跳转界面

    $btn.attr('disabled', true);
    $btn.removeClass("btn-normal").addClass("btn-press");
    var xhr = $.post($(form).attr("action"), vals, function (data) {
        if (data.code == '-1') {
            alertTrue(function () {
                location.href = '/user/user';
            })
        } else if (data.code != 0 && to == 1 && data.tourl != undefined) {
            toasturl(data.tourl, data.message);
        } else {
            if (data.code != 0) {
                toast(form, data.message, function () {
                    falsed(data);
                });
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
//toast('.notice','您输入的卡号有误');
function toast(btn, val, active)
{
    var kahao = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>');
    $(kahao).insertAfter($('form'));
    setTimeout(function () {
        $(kahao).fadeOut();
        setTimeout(function () {
            $(kahao).remove();
        }, 200);
        active();
    }, 2000);
}

//没有遮罩的弹窗有页面跳转
//toast('.notice','您输入的卡号有误');
function toasturl(url, val, active) {
    var kahao = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>');
    $(kahao).insertAfter($('form'));
    setTimeout(function () {
        $(kahao).fadeOut();
        setTimeout(function () {
            $(kahao).remove();
            window.location.href = url;
        }, 200);
    }, 2000);
}

//有遮罩的弹窗
//toastWithMast('.notice');
function toastWithMast(btn, val) {
    var kahao = $('<div class="mask" style="display: block"></div><div class="succeed-info show"><div class="col-xs-12"><img src="/images/succeed.png" alt="对钩" /> </div><div class="col-xs-12">' + val + '</div> </div>');
    $(kahao).insertAfter($('form'));
    setTimeout(function () {
        $(kahao).animate({opacity: 0}, 1000, function () {
            $(this).remove();
        });
    }, 2000);
}

//有遮罩的弹窗
//toastWithMast('.notice');
function toastWithMastUrl(url, val) {
    var kahao = $('<div class="mask" style="display: block"></div><div class="succeed-info show"> <div class="col-xs-12"><img src="/images/succeed.png" alt="对钩"/> </div> <div class="col-xs-12">' + val + '</div> </div>');
    $(kahao).insertAfter($('form'));
    setTimeout(function () {
        $(kahao).animate({opacity: 0}, 1000, function () {
            $(this).remove();
            location.href = url
        });
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

//有确定取消的弹窗
//alertTrueAndFalse(function(){
//    alert('true');
//},function(){
//    alert('false');
//});
function alertTrueAndFalse(tured, falsed) {
    var tishi = $('<div class="mask" style="display: block"></div> <div class="bing-info" style="display:block"> <div class="bing-tishi">提示</div> <p>绑定的银行卡将作为唯一充值，提现银行卡</p > <div class="bind-btn"> <span class="bind-xian">取消</span> <span>确定</span> </div> </div>');
    $(tishi).insertAfter($('form'));
    $('.bind-btn span').on('click', function () {
        var index = $('.bind-btn span').index(this);
        if (index == 1) {
            $(tishi).remove();
            tured();
        } else if (index == 0) {
            $(tishi).remove();
            falsed();
        }
    });
}

//只有确定按钮的弹窗
//alertTrueVal('你输入的密码错误',function(){
//    alert('true');
//});
function alertTrueVal(val, trued) {
    var chongzhi = $('<div class="mask" style="display:block;"></div><div class="bing-info show"> <div class="bing-tishi">温馨提示</div> <p class="tishi-p"> ' + val + '，即将跳转到相应的页面完成相应的操作</p > <div class="bind-btn"> <span class="true">我知道了</span> </div> </div>');
    $(chongzhi).insertAfter($('form'));
    $('.bing-info').on('click', function () {
        $(chongzhi).remove();
        trued();
    })
}

function createSms(phoneId, uid, captchaCodeId, fun) {
    var phone = $(phoneId).val();
    var captchaCode = $(captchaCodeId).val();
    var type = 3;
    if (!uid) {
        uid = phone;
        type = 1;
    } else if (uid == 'r') {
        uid = phone;
        type = 2;
    }
    var csrf = $("meta[name=csrf-token]").attr('content');
    $.post("/site/createsmscode", {uid: uid, type: type, phone: phone, captchaCode: captchaCode, _csrf: csrf}, function (result) {
        if (result.code == 0) {
            fun();
        } else {
            toast(phoneId, result.message);
            $("#captchaform-captchacode-image").click();
        }
    })
}



/*
 * 身份证15位编码规则：dddddd yymmdd xx p
 * dddddd：6位地区编码
 * yymmdd: 出生年(两位年)月日，如：910215
 * xx: 顺序编码，系统产生，无法确定
 * p: 性别，奇数为男，偶数为女
 *
 * 身份证18位编码规则：dddddd yyyymmdd xxx y
 * dddddd：6位地区编码
 * yyyymmdd: 出生年(四位年)月日，如：19910215
 * xxx：顺序编码，系统产生，无法确定，奇数为男，偶数为女
 * y: 校验码，该位数值可通过前17位计算获得
 *
 * 前17位号码加权因子为 Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ]
 * 验证位 Y = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]
 * 如果验证码恰好是10，为了保证身份证是十八位，那么第十八位将用X来代替
 * 校验位计算公式：Y_P = mod( ∑(Ai×Wi),11 )
 * i为身份证号码1...17 位; Y_P为校验码Y所在校验码数组位置
 */
function validateIdCard(idCard) {
    //15位和18位身份证号码的正则表达式
    var regIdCard = /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

    //如果通过该验证，说明身份证格式正确，但准确性还需计算
    if (regIdCard.test(idCard)) {
        if (idCard.length == 18) {
            var idCardWi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); //将前17位加权因子保存在数组里
            var idCardY = new Array(1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
            var idCardWiSum = 0; //用来保存前17位各自乖以加权因子后的总和
            for (var i = 0; i < 17; i++) {
                idCardWiSum += idCard.substring(i, i + 1) * idCardWi[i];
            }

            var idCardMod = idCardWiSum % 11;//计算出校验码所在数组的位置
            var idCardLast = idCard.substring(17);//得到最后一位身份证号码

            //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
            if (idCardMod == 2) {
                if (idCardLast == "X" || idCardLast == "x") {
                    return true;
                } else {
                    return false;
                }
            } else {
                //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
                if (idCardLast == idCardY[idCardMod]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    } else {
        return false;
    }
}