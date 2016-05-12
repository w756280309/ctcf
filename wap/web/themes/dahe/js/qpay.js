function validateBinding() {
    if ($.trim($('#card_no').val()) == '') {
        toast('银行卡号不能为空');
        return false;
    }
    var reg = /^[0-9]{16,19}$/;
    if (!reg.test($.trim($('#card_no').val()))) {
        toast('你输入的银行卡号有误');
        return false;
    }
    if ($('#bank_name').val() == '') {
        toast('开户行不能为空');
        return false;
    }
    if ($('#phone').val() == '') {
        toast('手机号码不能为空');
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
        if ($form.data('submitting')) {
            return;
        }
        $form.data('submitting', true);
        var xhr = $.post(
                '/user/qpay/binding/verify',
                $form.serialize()
                );

        xhr.done(function (data) {
            toast('转入联动优势进行绑卡操作');
            setTimeout(function () {
                window.location.href = data.next;
            }, 1500);
        });

        xhr.fail(function (jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

            toast(errMsg);
        });

        xhr.always(function () {
            $form.data('submitting', false);
            qpay_dismissConfirmModal();
        });
    });
});
