$(function () {
    FastClick.attach(document.body);

    // iScroll

    // var myScroll = new iScroll('wrapper',{
    //     vScrollbar:false,
    //     hScrollbar:false,
    //     momentum:false
    // });
    var initScroll = function () {
        var myScroll;
        intervalTime = setInterval(function () {
            var resultContentH = $("#wrapper ul").height();
            if (resultContentH > 0) {  //判断数据加载完成的条件
                clearInterval(intervalTime);
                myScroll = new iScroll('wrapper',{
                    vScrollbar:false
                });
            }
        }, 1);
    };
    initScroll();

    // var bW = $('.banner-opportunity').width() / 2 + 16;
    // $('.banner-opportunity').css({marginLeft: -bW});
    $(window).load(function () {
        // var lH = $('.login-box').height() / 2;
        // $('.login-box').css({marginTop: -lH});
        // 关闭登录弹窗
        $('.close-img').on('click', function () {
            $('.login-box').removeClass('login-box1').hide();
            $('.mark-box').fadeOut();
        });
        // 关闭无奖品列表弹窗
        $('.close-nogift').on('click', function () {
            $('.nogift-box').removeClass('nogift-box1').hide();
            refreshPage();
            $('.mark-box').fadeOut();
            $('body').unbind('touchmove');
        });
        // 关闭奖品列表弹窗
        $('.close-prize').on('click', function () {
            $('.myprize-box').removeClass('myprize-box1').hide();
            refreshPage();
            $('.mark-box').fadeOut();
            $('body').unbind('touchmove');
        });
        // 点击遮罩层关闭所有弹窗
        $('.mark-box').on('click', function () {
            $('.nogift-box').removeClass('nogift-box1').hide();
            $('.myprize-box').removeClass('myprize-box1').hide();
            $('.login-box').removeClass('login-box1').hide();
            $('.card-box').removeClass('card-box1').hide();
            $('.mark-box').hide();
        });
        $('.close-card').on('click', function () {
            $('.card-box').removeClass('card-box1').hide();
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
function eventTarget(event) {
    event.preventDefault();
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