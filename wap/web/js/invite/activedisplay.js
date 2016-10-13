$(document).ready(function () {
    FastClick.attach(document.body);
    $('.transform-box').height($('.transform-front-box').height());
    $('.content-picture').on('click', function () {
        $(this).parents('.fixed-box').hide().siblings('.fixed-float').hide();
    });
    $('#transform-icon').on('click', function () {
        if ($(this).hasClass('clickFlag')) {
            return false;
        }
        $(this).addClass('clickFlag');
        var xhr = $.get('/site/session');
        var _this = $(this);

        xhr.done(function(data) {
            if (typeof data.isLoggedin !== 'undefined' && data.isLoggedin) {
                toastCenter('注册奖励仅限新用户哦，邀好友来注册吧！', function(){
                    _this.removeClass('clickFlag');
                });
            } else {
                _this.addClass('transform-start');
                setTimeout(function () {
                    $('.transform-front-box').fadeOut(200);
                    $('.transform-back-box').fadeIn(200);
                }, 500);
            }
        });

        xhr.fail(function(jqXHR) {
            _this.removeClass('clickFlag');
            alert('请求失败');
        });
    });
    $('#collect').bind('click', function(e) {
        e.preventDefault();
        var _this = $(this);
        var xhr = $.get('/site/session');

        xhr.done(function(data) {
            if (typeof data.isLoggedin !== 'undefined' && data.isLoggedin) {
                var conf = confirm('您已注册，快去邀请好友，共享实惠');
                if (conf) {
                    location.href = '/';
                }
                return false;
            }
            location.href = _this.attr('href');
        });

        xhr.fail(function(jqXHR) {
            alert('请求失败');
        });
    });
});