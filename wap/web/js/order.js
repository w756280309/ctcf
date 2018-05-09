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
            if (2 === data.code) {
                var totalCount = data.coupon.count;
                var totalAmount = data.coupon.amount;
                if (totalCount > 0) {
                    $('#selectedCouponCount').val(totalCount);
                    $('#selectedCouponAmount').val(totalAmount);
                    var couponBoxInfo = totalAmount + '元（共' + totalCount + '张）';
                    $('#updateCouponBox').html(couponBoxInfo);
                } else {
                    toastCenter(data.message);
                    setTimeout(function () {
                        window.location.reload();
                    },2000)
                }
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
    var count = $('#selectedCouponCount').val();
    var amount = $('#selectedCouponAmount').val();
    var rate = $('#selectedCouponRate').val();
    var day = $('#selectedCouponRateday').val();
    var message = '';
    if ('undefined' !== typeof count) {
        message = '您有'+validCouponCount+'张优惠券可用，目前已选择'+count+'张代金券可抵扣'+WDJF.numberFormat(amount, true)+'元';
    } else {
        message = '您有'+validCouponCount+'张优惠券可用，本次未使用优惠券抵扣，点击确定立即投资';
    }
    if(count == 1 && amount == 0){
        message = '您有'+validCouponCount+'张优惠券可用，目前已选择1张加息券可'+day+'天加息'+rate;
    }
  layer.open({
    title: [
      '温馨提示',
      'background-color: #ff6058; color:#fff;'
    ]
    ,content: message
    ,shadeClose:false
    ,className: 'customer-layer-popuo'
    ,btn: ['确定', '取消']
    ,no: function(index){
      layer.closeAll();
    }
    ,yes: function(index){
      $('#couponConfirm').val('1');
      order();
      $('#couponConfirm').val('');
      layer.closeAll();
    }
  });
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

    var couponMoney = $('#selectedCouponAmount').val();
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

    $.post('/order/order/interest', {'sn': sn, '_csrf': csrf, 'amount': money}, function (data) {
        if (data && data.code === 0 && data.interest) {
            $('.yuqishouyi').html(WDJF.numberFormat(data.interest, false) + "元");
        } else {
            $('.yuqishouyi').html("0.00元");
        }
    });
}
