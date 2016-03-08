/**
 * Created by lcl on 2015/11/16.
 */
var csrf;
$(function () {
    csrf = $("meta[name=csrf-token]").attr('content');
    var $buy = $('#buybtn');
    var $form = $('#orderform');
    $form.on('submit', function (e) {
        e.preventDefault();

        if ($('#money').val() == '') {
            toast('投资金额不能为空');
            return false;
        }

        $buy.attr('disabled', true);
        var vals = $("#orderform").serialize();
        var xhr = $.post($("#orderform").attr("action"), vals, function (data) {
            if (data.code == 0) {
                toast('转入联动优势');
            } else {
                toast(data.message);
            }
            if (data.tourl != undefined) {
                setTimeout(function() {
                    location.href = data.tourl;
                }, 1000);
            }
        });
        xhr.always(function () {
            $buy.attr('disabled', false);
        })
    });
    $('#money').on('blur', function () {
        money = $(this).val();
        if (isNaN(money)) {
            money = 0;
            $(this).focus();
            return false;
        }

        $('.yuqishouyi').html(accDiv(accMul(accMul(money, yr), qixian), 360).toFixed(2) + "元");
    });
})


