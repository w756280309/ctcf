<?php

use yii\helpers\Html;

$this->title = '购买';
$this->backUrl = '/credit/note/detail?id='.$respData['id'];
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/buy-setting/order.css?v=201800212', ['depends' => 'wap\assets\WapAsset']);

use common\utils\StringUtils;

$note_config = json_decode($respData['config'], true);

$nowTime = new \DateTime();
$endTime = new \DateTime($respData['endTime']);
$isClosed = $respData['isClosed'] || $nowTime >= $endTime;
?>

<div class="row produce">
    <div class="col-xs-11 col-xs-offset-1 text-align-lf first-line" style="padding-right: 0;">【转让】<?= Html::encode($loan->title)?></div>
    <div class="col-xs-3 col-xs-offset-1">预期年化收益</div>
    <div class="col-xs-8 text-align-lf col"><?= number_format($order->yield_rate * 100, 1) ?>%</div>
    <div class="col-xs-3 col-xs-offset-1">剩余期限</div>
    <div class="col-xs-8 text-align-lf col">
        <?php
            $remainingDuration = $loan->getRemainingDuration();
            if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                echo $remainingDuration['months'] . '个月';
            }
            if (isset($remainingDuration['days'])) {
                if (!isset($remainingDuration['months']) || $remainingDuration['days'] > 0) {
                    echo $remainingDuration['days'] . '天';
                }
            }
        ?>
    </div>
    <div class="col-xs-3 col-xs-offset-1">可投余额</div>
    <div class="col-xs-8 text-align-lf col"><?= $isClosed ? '0.00' : StringUtils::amountFormat2(bcdiv(bcsub($respData['amount'], $respData['tradedAmount']), 100, 2)) ?>元</div>
    <div class="col-xs-3 col-xs-offset-1">折让率</div>
    <div class="col-xs-8 text-align-lf col"><?= StringUtils::amountFormat3($respData['discountRate']) ?>%</div>
</div>
<div class="row surplus margin-top">
    <div class="col-xs-3 col-xs-offset-1">可用余额</div>
    <div class="col-xs-6 safe-lf padding-left-20"><?= StringUtils::amountFormat3($user->lendAccount->available_balance) ?>元</div>
    <div class="col-xs-2 safe-txt"><a class="rg" href="/user/userbank/recharge?from=<?= urlencode('/credit/order/order?note_id='.$respData['id'])?>">去充值</a></div>
</div>
<form action="/credit/order/new" method="post" id="orderform" data-to="1">
<input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>" />
<input type="hidden" name="note_id" value="<?= $respData['id'] ?>">
<div class="row sm-height border-bottom">
    <div class="col-xs-3 col-xs-offset-1 safe-txt font-32">出借金额</div>
    <input name="amount" type="text" id="money" value="" t_value="" AUTOCOMPLETE="off" placeholder="起借<?= StringUtils::amountFormat2(bcdiv($note_config['min_order_amount'], 100, 2)) ?>元，递增<?= StringUtils::amountFormat2(bcdiv($note_config['incr_order_amount'], 100, 2)) ?>元"  class="col-xs-7 safe-lf text-align-lf font-26" onkeyup="if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}">
    <div class="safe-txt font-32 money_yuan">元</div>
</div>

<div class="row shouyi margin-top">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">应付利息</div>
    <div class="col-xs-8 safe-lf common_color common-pad" id="interest"><span>0.00</span>元</div>
</div>
<div class="row shouyi">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">实际支付</div>
    <div class="col-xs-8 safe-lf common_color shijizhifu common-pad" id="payAmount"><span>0.00</span>元</div>
</div>
<div class="row shouyi">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">预计收益</div>
    <div class="col-xs-8 safe-lf common_color yuqishouyi common-pad" id="profit"><span>0.00</span>元</div>
</div>

<div class="row login-sign-btn ht">
    <div class="col-xs-6 col-xs-offset-3 text-align-ct">
        <input id="buybtn" class="btn-common btn-normal" type="submit" value="购买">
    </div>
    <div class="col-xs-3 empty_div"></div>
</div>
</form>
<div class="row surplus">
    <div class="col-xs-12 text-align-ct bottom_center">
        查看<a href="/order/order/agreement?id=<?=$loan['id']?>&note_id=<?=$respData['id']?>">《认购协议》</a><a href="/credit/note/risk-note?type=2&loanId=<?= $loan['id'] ?>">《风险揭示》</a>
    </div>
</div>
<p id="btn_udesk_im">
    <a href="/site/udesk">
        <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/online-service-blue.png">在线客服
    </a>
</p>
<script>
    var currentInterest = <?= floatval($respData['currentInterest'] / 100) ?>;
    var remainingInterest = <?= floatval($respData['remainingInterest'] / 100) ?>;
    var total_amount = <?= floatval($respData['amount'] / 100) ?>;
    var rate = '<?= floatval($respData['discountRate']) ?>';

    function callback(data)
    {
        if (data.interest) {
            $('#interest span').html(data.interest);
        }

        if (data.profit) {
            $('#profit span').html(data.profit);
        }

        if (data.payment) {
            $('#payAmount span').html(data.payment);
        }
    }

    function profit(obj)
    {
        var amount = parseFloat($(obj).val());

        if (!amount) {
            amount = 0;

            $('#interest span').html('0.00');
            $('#profit span').html('0.00');
            $('#payAmount span').html('0.00');
        }

        if (amount > 0) {
            //请求交易系统计算进行计算
            $.ajax({
                type: "get",
                url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/v1/tx/credit-note/calc",
                data: { note_id: <?= $respData['id']?>, amount: amount, rate: <?= $respData['discountRate'] ?>},
                dataType: "jsonp"
            });
            //计算预期收益,再次调用计算应付利息与预计收益的函数
        }
    }

    $(function () {
        var $buy = $('#buybtn');
        var $form = $('#orderform');
        $form.on('submit', function (e) {
            e.preventDefault();

            if ($('#money').val() == '') {
                toast('出借金额不能为空');
                return false;
            }

            if ($('#money').val() <= 0) {
                toast('出借金额应大于0');
                return false;
            }

            $buy.attr('disabled', true);
            $buy.val('出借中...');
            var vals = $("#orderform").serialize();

            var xhr = $.post($("#orderform").attr("action"), vals, function (data) {
                if (data.code != 0) {
                    toast(data.message);
                }
                if (typeof data.url !== 'undefined' && data.url !== '') {
                    setTimeout(function () {
                        location.replace(data.url);
                    }, 1000);
                }
            });

            xhr.always(function () {
                $buy.attr('disabled', false);
                $buy.val("购买");
            });
        });

        $('#money').on('keyup', function () {
            profit(this);
        });
    });
</script>
