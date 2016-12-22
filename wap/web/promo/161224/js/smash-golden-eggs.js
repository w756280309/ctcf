$(function () {
    FastClick.attach(document.body);
    var ei = 0;
    var et = setInterval(function () {
        $('.banner-egg').attr({src: cdn + 'promo/1607/images/smash-golden-eggs/egg.png'});
        $('.banner-egg').eq(ei).attr({src: cdn + 'promo/1607/images/smash-golden-eggs/eggs.png'});
        ei++;
        if (ei > 2) {
            ei = 0;
        }
    }, 1000);
    var bW = $('.banner-opportunity').width() / 2 + 16;
    $('.banner-opportunity').css({marginLeft: -bW});
    $(window).load(function () {
        $('.banner-egg').bind('click', function () {
            $('.banner-egg').unbind('click');
            clearInterval(et);
            var index = $('.banner-egg').index(this);
            $('.banner-egg').attr({src: cdn + 'promo/1607/images/smash-golden-eggs/egg.png'});
            for (var i = 0; i <= $('.banner-egg').length; i++) {
                var bE = $('.banner-egg').eq(i).attr('data-n');
                if (bE == 'true') {
                    $('.banner-egg').eq(i).attr({src: cdn + 'promo/1607/images/smash-golden-eggs/egg1.png?v=1.1'});
                }
            }
            $('.banner-chui').eq(index).show();
            $('.banner-chui').eq(index).css({
                '-webkit-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
                '-o-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
                '-moz-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
            });
            setTimeout(function () {
                $('.banner-lie0').eq(index).fadeIn();
                eggClick(index);
            }, 600);
        });
        function eggClick(index) {
            var s = 1;
            var t = window.setInterval(function () {
                $('.banner-lie0').eq(index).attr('src', cdn + 'promo/1607/images/smash-golden-eggs/liewen' + s + '.png');
                s++;
                if (s == 5) {
                    $('.banner-chui').eq(index).hide();
                    window.clearInterval(t);
                    $('.banner-lie0').hide();
                    $('.banner-egg').eq(index)[0].src = cdn + 'promo/1607/images/smash-golden-eggs/egg1.png?v=1.1';
                    $('.banner-egg').eq(index).attr({'data-n': 'true'});
                    $('.banner-hua').eq(index).fadeIn();
                    $.ajax({
                        url: '/promotion/p161224/draw',
                        type: "get",
                        dataType: "json",
                        success: function (data) {
                            if (data.code == 101) { //登录后再砸吧
                                $('.login-inner').html(data.msg);
                                $('.login-img img').attr({src: cdn + 'promo/1607/images/smash-golden-eggs/nologin.png'});
                                $('.mark-box').fadeIn();
                                $('.login-box').show();
                                setTimeout(function () {
                                    $('.login-box').addClass('login-box1');
                                }, 100);
                                $('.login-btn span').html('立即登录');
                                $('.login-btn').attr({href: '/site/login'});
                            } else if (data.code == 102) { //没有砸蛋机会,去投资(理财列表)
                                $('.login-inner').html(data.msg);
                                $('.login-img img').attr({src: cdn + 'promo/1607/images/smash-golden-eggs/noChange.png'});
                                $('.mark-box').fadeIn();
                                $('.login-box').show();
                                setTimeout(function () {
                                    $('.login-box').addClass('login-box1');
                                }, 100);
                                $('.login-btn span').html('去投资');
                                $('.login-btn').attr({href: '/deal/deal/index'});
                            } else if (data.code == 200) {
                                $('.card-title').html(data.msg);
                                $('.mark-box').fadeIn();
                                $('.card-box').show();
                                setTimeout(function () {
                                    $('.card-box').addClass('card-box1');
                                }, 100);
                                if (data.isCoupon) { //代金券
                                    $('.card-btn span').html('查看奖品');
                                    $('.card-title').css({top: '46%'});
                                    $('.card-content').hide();
                                    $('.card-btn').attr({href: '/user/coupon/list'});
                                } else { //京东卡
                                    $('.card-btn span').html('确认');
                                    $('.card-btn span').on('click', function () {
                                        $('.card-box').removeClass('card-box1');
                                        setTimeout(function () {
                                            $('.card-box').hide();
                                            refreshPage();
                                        }, 500);
                                        $('.mark-box').fadeOut();
                                    })
                                }
                            } else if (data.code == 400) {
                                alert(data.msg);
                            }
                        }
                    });
                }
            }, 200);
        }

        var lH = $('.login-box').height() / 2;
        $('.login-box').css({marginTop: -lH});
        $('.close-img').on('click', function () {
            $('.login-box').removeClass('login-box1');
            setTimeout(function () {
                $('.login-box').hide();
                refreshPage();
            }, 500);
            $('.mark-box').fadeOut();
        });
        $('.mark-box').on('click', function () {
            $('.login-box').removeClass('login-box1');
            setTimeout(function () {
                $('.login-box').hide();
                refreshPage();
            }, 500);
            $('.card-box').removeClass('card-box1');
            setTimeout(function () {
                $('.card-box').hide();
                refreshPage();
            }, 500);
            $('.mark-box').fadeOut();
        });
        $('.close-card').on('click', function () {
            $('.card-box').removeClass('card-box1');
            setTimeout(function () {
                $('.card-box').hide();
                refreshPage();
            }, 500);
            $('.mark-box').fadeOut();
        });

    });
    getParamsString();
});

//刷新页面
function refreshPage() {
    var isweixin = isWeiXin();
    if (isweixin) {
        var paramsStr = getParamsString();
        if (paramsStr === '') {
            paramsStr = '?t=' + Math.random();
        } else {
            paramsStr = '&t=' + Math.random();
        }
        location.href = location.href + paramsStr;
    } else {
        location.reload();
    }
}
//微信浏览器location.reload() 写法,无法实现刷新
//判断是否是微信浏览器
function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}

//获取url参数
function getParamsString() {
    var url = location.search; //获取url中"?"符后的字串
    var paramsStr = '';
    if (url.indexOf("?") != -1) {
        //判断是否有参数
        paramsStr = url.substr(1);
    }
    return paramsStr;
}