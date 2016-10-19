function closeConfirmBox() {
    $('.mask').hide();
    $('.confirmBox').hide();
}
$(function(){
    $('.tr-click:odd').find('td').addClass('td-back-color');
})
//查看下拉
$(document).ready(function () {
    $('.tr-click:odd').find('td').addClass('td-back-color');
    $('.tip-cursor').on('click', function () {
        if ($(this).find('.tip-icon-enna').hasClass('tip-icon-top')) {
            $(this).parents('.tr-click').next('.tr-show').show();
            $(this).find('.tip-icon-enna').removeClass('tip-icon-top').addClass('tip-icon-bottom');
        } else {
            $(this).parents('.tr-click').next('.tr-show').hide();
            $(this).find('.tip-icon-enna').removeClass('tip-icon-bottom').addClass('tip-icon-top');
        }
    });

    $('.cancel-note').bind('click', function () {
        $('.mask').show();
        $('.confirmBox').show();
    });
    $('.confirmBox-left').bind('click', function () {
        closeConfirmBox();
    });
    $('.confirmBox-right').bind('click', function () {
        closeConfirmBox();
        var noteBtn = $('.cancel-note');
        if (noteBtn.hasClass('twoClick')) {
            return false;
        }

        noteBtn.addClass('twoClick');
        var id = noteBtn.attr('note-id');
        var xhr = $.get('/credit/trade/cancel?id=' + id, function (data) {
            if (0 === data.code) {
                $('.cancel-note').each(function () {
                    if ($(this).attr("note-id") === id) {
                        $(this).html('处理中');
                    }
                });
                return false;
            }
            alert(data.message);
            if (data.message === '转让已结束') {
                window.location.reload();
            }
        });

        xhr.always(function () {
            noteBtn.removeClass('twoClick');
        });

        xhr.fail(function () {
            noteBtn.removeClass('twoClick');
            alert('系统繁忙，撤销失败，请稍后重试!');
        });
    });
});