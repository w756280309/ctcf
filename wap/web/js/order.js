/**
 * Created by lcl on 2015/11/16.
 */
var csrf;

$(function () {
    csrf = $("meta[name=csrf-token]").attr('content');

    $('#orderform').on('submit', function (e) {
        e.preventDefault();

        if ('' === $('#money').val()) {
            toastCenter('投资金额不能为空');
            return;
        }

        order();
    });

    $('.x-cancel').on('click', function () {
        $('#mask').hide();
        $('#info').hide();
    });

    $('.x-confirm').on('click', function () {
        $('#couponConfirm').val('1');
        order();
        $('#couponConfirm').val('');
    });

    profit($("#money"));
});

function order() {
    var $buy = $('#buybtn');
    var $form = $('#orderform');

    $buy.attr('disabled', true);
    $buy.val('购买中...');

    $('#mask').hide();
    $('#info').hide();

    var vals = $form.serialize();
    var xhr = $.post($form.attr("action"), vals, function (data) {
        if (data.code) {
            if ('undefined' !== typeof data.confirm && 1 === data.confirm) {
                openPopup();
                return;
            }

            toastCenter(data.message);
        }

        if (data.tourl != undefined) {
            location.replace(data.tourl);
        }
    });

    xhr.always(function () {
        $buy.attr('disabled', false);
        $buy.val("购买");
    });
}

function openPopup() {
    var couponId = $('#couponId').val();
    var couponMoney = $('#couponMoney').val();
    var message = '';

    if ('undefined' !== typeof couponId) {
        message = '您有'+validCouponCount+'张代金券可用，将使用'+WDJF.numberFormat(couponMoney, true)+'元代金券一张，点击确定立即投资';
    } else {
        message = '您有'+validCouponCount+'张代金券可用，本次未使用代金券抵扣，点击确定立即投资';
    }

    $('#info p').html(message);
    $('#mask').show();
    $('#info').show();
}

function profit($this)
{
    var money = $this.val();
    retmet = parseInt(retmet);

    money = money.replace(/^[^0-9]+/, '');
    if(!$.isNumeric(money)) {
        money = 0;
    }

    if (money <= 0) {
        $('.shijizhifu').html('0.00元');
        $('.yuqishouyi').html('0.00元');

        return;
    }

    var couponMoney = $('#couponMoney').val();
    if ('undefined' !== typeof couponMoney) {
        couponMoney = couponMoney.replace(/^[^0-9]+/, '');
        if(!$.isNumeric(couponMoney)) {
            couponMoney = 0;
        }
        var m = accSub(money, couponMoney);
        $('.shijizhifu').html(WDJF.numberFormat((m > 0) ? m : money , false) + "元");
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
            displayProfit(money, rate, qixian, retmet);
        });
    } else {
        displayProfit(money, yr, qixian, retmet);
    }
}

function displayProfit(money, rate, expire, refundMethod)
{
    if (refundMethod === 1) {
        expireProfit = accDiv(accMul(accMul(money, rate), expire), 365);
    } else if(refundMethod === 10) {
        var monthRate = accDiv(rate, 12);
        expireProfit = accSub(
            accMul(
                accDiv(
                    accMul(
                        accMul(
                            money
                            , monthRate
                        )
                        , Math.pow(
                            accAdd(
                                1
                                , monthRate
                            )
                            , expire
                        )
                    ), accSub(
                        Math.pow(
                            accAdd(
                                1
                                , monthRate
                            )
                            , expire
                        )
                        , 1
                    )
                )
                , expire
            )
            , money
        );
        console.log(expireProfit);
    } else {
        expireProfit = accDiv(accMul(accMul(money, rate), expire), 12);
    }
    $('.yuqishouyi').html(WDJF.numberFormat(expireProfit, false) + "元");
}
