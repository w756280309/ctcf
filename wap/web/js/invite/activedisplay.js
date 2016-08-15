$(document).ready(function () {
    FastClick.attach(document.body);
    $('.transform-box').height($('.transform-front-box').height());
    $('.content-picture').on('click', function () {
        $(this).parents('.fixed-box').hide().siblings('.fixed-float').hide();
    });
    $('#transform-icon').on('click', function () {
        $(this).addClass('transform-start');
        setTimeout(function () {
            $('.transform-front-box').fadeOut(200);
            $('.transform-back-box').fadeIn(200);
        }, 500);
    });
    $('#collect').bind('click', function(e) {
        e.preventDefault();
        var _this = $(this);
        var xhr = $.get('/site/session');

        xhr.done(function(data) {
            if (typeof data.isLoggedin !== 'undefined' && data.isLoggedin) {
                alert('您已注册，快去邀请好友，共享实惠');
                return false;
            }
            location.href = _this.attr('href');
        });

        xhr.fail(function(jqXHR) {
            alert('请求失败');
        });
    });
});