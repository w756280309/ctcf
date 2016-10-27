function closeConfirmBox()
{
    $('.mask').hide();
    $('.confirmBox').hide();
}

function alertReferBox()
{
    $('.mask').show();
    $('.confirmBox').show();
}

$(function() {
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

    $('.cancel-operation').on('click', '.cancel-note', function () {
        $(this).addClass('refered');
        alertReferBox();
    });

    $('.confirmBox-bottom').on('click', '.confirmBox-left', function () {
        $('.cancel-note').removeClass('refered');
        closeConfirmBox();
    });

    $('.confirmBox-bottom').on('click', '.confirmBox-right', function () {
        closeConfirmBox();
        var noteBtn = $('.refered');
        if (noteBtn.hasClass('twoClick')) {
            return false;
        }

        noteBtn.addClass('twoClick');
        var id = noteBtn.attr('note-id');
        var xhr = $.get('/credit/trade/cancel?id=' + id, function (data) {
            $('.cancel-note').removeClass('refered');
            if (0 === data.code) {
                $('.cancel-note').each(function () {
                    if ($(this).attr('note-id') === id) {
                        $(this).html('处理中');
                        $(this).removeClass('cancel-note');
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
            noteBtn.removeClass('twoClick refered');
        });

        xhr.fail(function () {
            noteBtn.removeClass('twoClick refered');
            alert('系统繁忙，撤销失败，请稍后重试!');
        });
    });
});