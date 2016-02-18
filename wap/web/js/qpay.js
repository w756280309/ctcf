function toast(btn, val) {
    var $alert = $(
            '<div class="error-info" style="display: block;">'
            + '<div>'
            + val
            + '</div>'
            + '</div>'
            );
    $alert.appendTo('body');
    setTimeout(function () {
        $alert.fadeOut(500, function () {
            $alert.remove();
        });
    }, 2000);
}

function toasturl(url, val) {
    var $kahao = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>')
            .insertAfter($('form'));

    setTimeout(function () {
        $kahao.fadeOut();
        setTimeout(function () {
            $kahao.remove();
            window.location.href = url;
        }, 200);
    }, 2000);
}

function validateBinding() {
    if ($('#card_no').val() == '') {
        toast(null, '银行卡号不能为空');
        return false;
    }
    if ($('#bank_name').val() == '') {
        toast(null, '开户行不能为空');
        return false;
    }
    if ($('#phone').val() == '') {
        toast(null, '手机号码不能为空');
        return false;
    }
    return true;
}

function qpay_showConfirmModal() {
    var $mask = $('#qpay-binding-confirm-mask');
    var $diag = $('#qpay-binding-confirm-diag');
    $mask.show();
    $diag.show();
}

function qpay_dismissConfirmModal() {
    var $mask = $('#qpay-binding-confirm-mask');
    var $diag = $('#qpay-binding-confirm-diag');
    $mask.hide();
    $diag.hide();
}

$(function () {
    $('#qpay-binding-confirm-diag').on('click', '.x-cancel', function () {
        qpay_dismissConfirmModal();
    }).on('click', '.x-confirm', function () {
        var $form = $('#form');
        var xhr = $.post(
                '/user/qpay/binding/verify',
                $form.serialize()
                );

        xhr.done(function (data) {
            toast(null, '转入联动优势进行绑卡操作');
            setTimeout(function () {
                window.location.href = data.next;
            }, 1500);
        });

        xhr.fail(function (jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

            toast(null, errMsg);
        });

        xhr.always(function () {
            qpay_dismissConfirmModal();
        });
    });
});
