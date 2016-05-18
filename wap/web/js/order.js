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
        $buy.val("购买中……");
        var vals = $("#orderform").serialize();
        var xhr = $.post($("#orderform").attr("action"), vals, function (data) {
            if (data.code == 0) {
                //toast('投标成功');
            } else {
                toast(data.message);
            }
            if (data.tourl != undefined) {
                setTimeout(function () {
                    location.replace(data.tourl);
                }, 1000);
            }
        });
        xhr.always(function () {
            $buy.attr('disabled', false);
            $buy.val("购买");
        })
    });

    $('#money').on('keyup', function () {
        $('#couponMoney').val('');  //消除提交表单信息
        $('#couponId').val('');

        profit($(this));
        var url = toCoupon(true);

        $.get(url, function(data)
        {
            if (data.data.length > 0) {
                $('.coupon-title').html("使用代金券");
                $('.coupon-content').html("<div class=\"col-xs-8 safe-txt\" onclick=\"toCoupon()\">请选择</div>");
            } else {
                $('.coupon-title').html("使用代金券");
                $('.coupon-content').html("<div class=\"col-xs-8 safe-txt\">无可用</div>");
            }
        });
    });

    profit($("#money"));
});

function profit($this)
{
    var money = $this.val();
    money = money.replace(/^[^0-9]+/, '');
    if(!$.isNumeric(money)) {
        money = 0;
    }

    var couponMoney = $('#couponMoney').val();
    if ('undefined' !== typeof couponMoney) {
        couponMoney = couponMoney.replace(/^[^0-9]+/, '');
        if(!$.isNumeric(couponMoney)) {
            couponMoney = 0;
        }
        $('.shijizhifu').html(WDJF.numberFormat(Subtr(money, couponMoney), false) + "元");
    } else {
        $('.shijizhifu').html(WDJF.numberFormat(money, false) + "元");
    }

    if(isFlexRate) {
        $.post('/order/order/rate', {'sn': sn, '_csrf': csrf, 'amount': money}, function(data)
        {
            if(true === data.res) {
                rate = data.rate;
            } else {
                rate = yr;
            }
            if(1 == parseInt(retmet)) {
                $('.yuqishouyi').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 365), false) + "元");
            } else {
                $('.yuqishouyi').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 12), false) + "元");
            }
        });
    } else {
        if(1 == parseInt(retmet)) {
            $('.yuqishouyi').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 365), false) + "元");
        } else {
            $('.yuqishouyi').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 12), false) + "元");
        }
    }
}