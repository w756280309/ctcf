var WDJF = {};
/**
 * 为金额添加千分位显示
 * @param double|string amount 金额的数字或字符串
 * @param boolean stripTrailingZeros 末尾是否保留0标志位,为真时,去掉小数点右边多余的零,否则保留两位小数
 * @returns string 金额的千分位显示字符串
 */
WDJF.numberFormat = function (amount, stripTrailingZeros) {
    if (stripTrailingZeros) {
        return (new Number(amount) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    } else {
        return (new Number(amount).toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    }
};
function accDiv(arg1, arg2) {
    var t1 = 0, t2 = 0, r1, r2;
    try {
        t1 = arg1.toString().split(".")[1].length
    } catch (e) {
    }
    try {
        t2 = arg2.toString().split(".")[1].length
    } catch (e) {
    }
    with (Math) {
        r1 = Number(arg1.toString().replace(".", ""))
        r2 = Number(arg2.toString().replace(".", ""))
        return (r1 / r2) * pow(10, t2 - t1);
    }
}
function accMul(arg1, arg2) {
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length
    } catch (e) {
    }
    try {
        m += s2.split(".")[1].length
    } catch (e) {
    }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
}

function mianmi(){
    var html = '<!--类mask为遮罩层--><div class="mask" style="display: none;"></div> <!--类pop-open为开通免密--><div class="result-pop pop-open mianmi_message" style="display:none;"> <p class="result-pop-hender">提示<img class="close" id = "box_close" src="/images/login/close.png" alt="" style = "float: right;margin-top: 15px;margin-right: 15px;cursor: pointer;"></p> <p class="result-pop-content">将为您开通免密支付功能，之后进行投资时，无需输入资金托管账户支付密码。但是，当您需要提现时，为确保您的资金安全，仍需输入支付密码。</p> <p class="result-pop-phone">如遇到问题请拨打我们的客服热线：400-101-5151(9:00~20:00)</p> <p><span class="link-confirm" id="mianmi_confirm" onclick="location.href=\'/user/qpay/binding/umpmianmi\'">确定</span></p> </div>';
    if($('body .mask').length == 0 && $('body .mianmi_message').length == 0){
        $('body').append(html);
    }
    var mask = $('.mask');
    var mianmi_message = $('.mianmi_message');
    mask.show();
    mianmi_message.show();

    $('#box_close').click(function(){
        mask.hide();
        mianmi_message.hide();
    });
}

