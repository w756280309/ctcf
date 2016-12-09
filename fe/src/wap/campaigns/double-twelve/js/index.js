/**
 * Created by lw on 2016/12/7.
 */
$(function () {
    FastClick.attach(document.body);
    initScroll();
    var len = $('.people ul li').length;
    if (len > 3) {
        lunBo();
    }
    $('.btest').on('click', function () {
        $('.mask').show();
        var classNmae = $(this).data('value');
        $('.' + classNmae).show();
        // $('html').attr('ontouchmove', 'event.preventDefault()');
       $('body').on('touchmove', eventTarget, false);
    });

    $('.mask,.closepop').on('click', function () {
        $('.pop').hide();
        $('.mask').hide();
        // $('html').removeAttr('ontouchmove');
        $('body').unbind('touchmove');

    });
});

function lunBo() {
    var liH = 0.693;
    var ts = setInterval(function () {
        $('.people ul li').eq(0).animate({"margin-top": "-" + liH + "rem"}, 1000, function () {
            $(this).css({"margin-top": 0});
            $(this).appendTo($('.people ul'));
        });
    }, 2000);
}

function eventTarget(event) {
    event.preventDefault();
}

var initScroll = function () {
    var myScroll;
    intervalTime = setInterval(function () {
        var resultContentH = $("#giftBox ul").height();
        if (resultContentH > 0) {  //判断数据加载完成的条件
            clearInterval(intervalTime);
            myScroll = new iScroll('giftBox',{
                vScrollbar:false
            });
        }
    }, 1);
}