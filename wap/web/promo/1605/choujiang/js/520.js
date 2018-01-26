$(function() {
    $('#no2,#no3,#no4,#no5,#no6').hide();
    $('#no1').show();

    $('.draw').on('click',function() {
        var tel = $('#phone').val();

        //手机号不能为空
        if (tel === '') {
            toast('手机号不能为空');
            return;
        }

        //验证手机号
        var regphone = /^0?1\d{10}$/;
        if (!regphone.test(tel)) {
            toast('手机号格式错误');
            return;
        }

        var xhr = $.get("/promotion/promo160520/draw", {mobile: tel});

        xhr.done(function(data) {
            if ('' === data.message) {
                $('#no1, #no2, #no3, #no4, #no5').hide();
                if (data.isNewUser) {
                    if (1 === data.prizeId) {
                        $('#no2').show();
                    } else if (2 === data.prizeId) {
                        $('#no3').show();
                    } else if (3 === data.prizeId) {
                        $('#no4').show();
                    }
                } else {
                    $('#no5').show();
                }
            } else {
                toast(data.message);
            }
        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                ? jqXHR.responseJSON.message
                : '未知错误，请刷新重试或联系客服';

            if (400 === jqXHR.status) {
                toast(errMsg);
            }

            if (500 === jqXHR.status) {
                if (1 === jqXHR.responseJSON.code) {
                    $('#no1, #no2, #no3, #no4, #no5').hide();
                    $('#no6').show();
                } else {
                    toast(errMsg);
                }
            }
        });
    });

    //立即注册赚起来
    $('.register').on('click', function() {
        location.href = '/site/signup';
    });

    //登录查看按钮
    $('.login').on('click', function() {
        location.href = '/site/login?next='+location.protocol+'\/\/'+location.hostname+'/user/user';
    });

    //返回首页按钮
    $('#index').on('click',function(){
        location.href = '/';
    });
})