$(document).ready(function () {
    $('.change-click').on('click', function () {
        var index_eq = $(this).parents('.change-single').index('.change-single');
        $(this).parents('.change-single').addClass('change-selected').siblings('.change-single').removeClass('change-selected');
        $('.container-box').eq(index_eq).show().siblings('.container-box').hide();
    })
    $('.container-left').on('click', function () {
        var index_left = $('.change-selected').index('.change-single');
        index_left--;
        $('.change-single').eq(index_left).addClass('change-selected').siblings('.change-single').removeClass('change-selected');
        $('.container-box').eq(index_left).show().siblings('.container-box').hide();
    });
    $('.container-right').on('click', function () {
        var index_right = $('.change-selected').index('.change-single');
        index_right++;
        $('.change-single').eq(index_right).addClass('change-selected').siblings('.change-single').removeClass('change-selected');
        $('.container-box').eq(index_right).show().siblings('.container-box').hide();
    });
    if (navigator.userAgent.indexOf("MSIE 8.0") > 0 || navigator.userAgent.indexOf("MSIE 9.0") > 0) {
        document.body.onselectstart = document.body.ondrag = function () {
            return false;
        }
    }
    $('html').on('keydown', function(e) {
        var index_left = $('.change-selected').index('.change-single');
        if (e.keyCode === 37 && index_left > 0) {
            index_left--;
        } else if (e.keyCode === 39 && index_left < 5) {
            index_left++;
        }
        $('.change-single').eq(index_left).addClass('change-selected').siblings('.change-single').removeClass('change-selected');
        $('.container-box').eq(index_left).show().siblings('.container-box').hide();
    })
});