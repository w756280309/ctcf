<?php
use common\utils\StringUtils;
use wap\assets\WapAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = '转让';

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/transfer_order.css?v=20161027', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.ba-throttle-debounce.min.js?v=161008', ['depends' => JqueryAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => JqueryAsset::class]);

$discountRate = Yii::$app->params['credit_trade']['max_discount_rate'];
$fee = Yii::$app->params['credit_trade']['fee_rate'] * 1000;
$minOrderAmount = bcdiv($asset['minOrderAmount'], 100, 2);
$incrOrderAmount = bcdiv($asset['incrOrderAmount'], 100, 2);
$calcDiscountRate = min($discountRate, bcmul(bcdiv($asset['currentInterest'], bcadd($asset['currentInterest'], $asset['maxTradableAmount'], 14), 14), 100, 2));
?>

<div class="row produce">
    <div class="col-xs-11 col-xs-offset-1 text-align-lf first-line" style="padding-right: 0;"><?= Html::encode($loan->title)?></div>
    <div class="col-xs-3 col-xs-offset-1">可转让金额</div>
    <div class="col-xs-8 text-align-lf col"><?= number_format(bcdiv($asset['maxTradableAmount'], 100, 2), 2)?>元</div>
    <div class="col-xs-3 col-xs-offset-1">预期年化率</div>
    <div class="col-xs-8 text-align-lf col">
        <?= floatval($apr) * 100 ?>%
    </div>
    <div class="col-xs-3 col-xs-offset-1">剩余期限</div>
    <div class="col-xs-8 text-align-lf col">
        <?php
            $remainingDuration = $loan->getRemainingDuration();
            if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                echo $remainingDuration['months'] . '<span>个月</span>';
            }
            if (isset($remainingDuration['days'])) {
                if (!isset($remainingDuration['months']) || $remainingDuration['days'] > 0) {
                    echo $remainingDuration['days'] . '<span>天</span>';
                }
            }
        ?>
    </div>
</div>
<div class="row sm-height margin-top">
    <div class="col-xs-3 col-xs-offset-1 safe-txt font-32">转让金额</div>
    <input type="text" name="" id="credit_amount" placeholder="起投<?= StringUtils::amountFormat2($minOrderAmount) ?>元，递增<?= StringUtils::amountFormat2($incrOrderAmount) ?>元" autocomplete="off" t_value="" onkeyup="if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}" class="col-xs-7 safe-lf text-align-lf font-26">
    <div class=" safe-txt font-32 money_yuan">元</div>
</div>
<div class="row sm-height">
    <div class="col-xs-3 col-xs-offset-1 safe-txt font-32">
        折让率
        <img class="common_img sm-height_tips_img" src="<?= ASSETS_BASE_URI ?>images/credit/icon_instruction.png" alt="">

    </div>
    <p class="sm-height_tips">
        <img src="<?= ASSETS_BASE_URI ?>images/credit/triangle.png" alt="">
        产品在转让时折让的比率，如折让率1%，则按照转让价值的99%来出售。
    </p>
    <input type="text" name="discount_rate" id="discount_rate_input" value=""  placeholder="不高于<?= $calcDiscountRate ?>%，可设置2位小数" autocomplete="off" maxlength="4" t_value="" onkeyup=" if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}" class="col-xs-7 safe-lf text-align-lf font-26">
    <div class="safe-txt font-32 money_yuan">%</div>
</div>
<div class="hide toRefer"></div>
<div class="row shouyi shouyi_space">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">应付利息</div>
    <div class="col-xs-8 safe-lf common_color"><span id="expect_money">0.00</span>元</div>
</div>
<div class="row shouyi">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">折让后价格</div>
    <div class="col-xs-8 safe-lf common_color shijizhifu"><span id="expect_amount">0.00</span>元</div>
</div>
<div class="row shouyi">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">
        手续费
        <img class="common_img shouyi_tips_img" src="<?= ASSETS_BASE_URI ?>images/credit/icon_instruction.png" alt="">
    </div>
    <div class="col-xs-8 safe-lf common_color yuqishouyi"><span id="fee">0.00</span>元</div>
    <!--tips-->
    <p class="col-xs-12 shouyi_tips">
        <img class="shouyi_tips_tra" src="<?= ASSETS_BASE_URI ?>images/credit/triangle.png" alt="">
        转让需要支付转让金额的<?= $fee ?>‰手续费，在成交后直接从成交金额中扣除。
    </p>
</div>
<div class="text-align-lf bottom_center rules-note"><a href="/credit/note/rules">转让规则</a></div>
<!--bottom-->
<div class="row login-sign-btn ht">
    <div class="col-xs-6 col-xs-offset-3 text-align-ct">
        <input id="credit_submit_btn" class="btn-common btn-normal" type="button" value="确定转让" onclick="validateData()">
    </div>
    <div class="col-xs-3 empty_div"></div>
</div>
<div class="row surplus">
    <div class="col-xs-12 text-align-ct bottom_center">查看<a href="/order/order/agreement?id=<?= $loan->id ?>&note_id=1">《转让协议》</a><a href="/credit/note/risk-note?type=1">《风险揭示》</a></div>
</div>
<script>
    $(function() {
        FastClick.attach(document.body);

        //提示信息的显示与隐藏
        var flag_shouyi = 0;
        var flag_sm= 0;
        $('.shouyi_tips_img').parent().bind('click',function(event){
            var event = event || window.event;
            if(!flag_shouyi){
                $('.shouyi_tips').show();
                $('.sm-height_tips').hide();
                flag_shouyi = 1;
                flag_sm = 0;
            } else {
                $('.shouyi_tips').hide();
                flag_shouyi = 0;
            }
            event.stopPropagation();
        });
        $('body').bind('click',function(){
            if(flag_shouyi){
                $('.shouyi_tips').hide();
                flag_shouyi = 0;
            }
        });

        $('.sm-height_tips_img').parent().bind('click',function(event){
            var event = event || window.event;
            if(!flag_sm){
                $('.sm-height_tips').show();
                $('.shouyi_tips').hide();
                flag_sm = 1;
                flag_shouyi = 0;
            } else {
                $('.sm-height_tips').hide();
                flag_sm = 0;
            }
            event.stopPropagation();
        });
        $('body').bind('click',function(){
            if(flag_sm){
                $('.sm-height_tips').hide();
                flag_sm = 0;
            }
        });
    });

    var submit_btn = $('#credit_submit_btn');
    var amount_input = $('#credit_amount');
    var discount_rate_input = $('#discount_rate_input');
    var current_interest = <?= floatval($asset['currentInterest'] / 100)?>;
    var total_amount = <?= floatval($asset['maxTradableAmount'] / 100)?>;
    var config_rate = <?= floatval($discountRate) ?>;
    var minAmount = <?= floatval($minOrderAmount) ?>;
    var incAmount = <?= floatval($incrOrderAmount) ?>;
    var feeRate = <?= floatval($fee / 1000) ?>;

    $(function() {
        amount_input.change($.throttle(100,function () {
            validateData();
        }));
        discount_rate_input.change(100,function () {
            validateData();
        });
    });

    function alertReferBox(val, callback)
    {
        var confirm = $('<div id="mask" class="mask" style="display: block;"></div><div id="bing_info" class="bing-info show" style="position: fixed;margin: 0px;left:15%"> <p class="tishi-p" style="line-height: 20px;">'+ val +'</p> <div class="bind-btn"> <span class="no" style="border-right: 1px solid #ccc;">取消</span> <span class="yes" style="border-left: 1px solid #ccc;">确定</span></div> </div>');
        if ($('#mask').length > 0) {
            $('#mask').remove();
        }
        if ($('#bing_info').length > 0) {
            $('#bing_info').remove();
        }
        $(confirm).insertAfter($('.toRefer'));
        $('.bind-btn .yes').on('click', function () {
            $(confirm).remove();
            if (typeof callback !== 'undefined') {
                callback();
            }
        });
        $('.bind-btn .no').on('click', function () {
            $(confirm).remove();
        });
    }

    function subConfirm()
    {
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        var amount = parseFloat(amount_input.val());
        if (!amount) {
            amount = 0;
        }

        var xhr = $.post('/credit/note/create', {
            '_csrf': '<?= Yii::$app->request->csrfToken?>',
            'asset_id': '<?= $asset['id']?>',
            'amount': amount,
            'rate': rate
        }, function (data) {
            var res = data['data'];
            if (data['code'] === 1) {
                for (var i = 0; i < res.length; i++) {
                    var err = res[i];
                    if (err['attribute'] === 'amount') {
                        toastCenter(err['msg']);
                        return false;
                    } else if (err['attribute'] === 'discountRate') {
                        toastCenter(err['msg']);
                        return false;
                    } else if (err['attribute'] === '' && err['msg']) {
                        toastCenter(err['msg'], function () {
                            if ('请登录' === err['msg']) {
                                location.href = '/site/login';
                            }
                        });

                        return false;
                    }
                }
            } else if (data['code'] === 0) {
                window.location.href = '/credit/note/res?res=success'
            }
        });
        xhr.fail(function () {
            toastCenter('系统繁忙，请稍后重试！');
        });
    }

    function validateData()
    {
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        if (rate < 0) {
            toastCenter('折让率不能小于0');
            return false;
        }
        var amount = amount_input.val();
        if ('' === amount) {
            toastCenter('请输入转让金额');
            return false;
        }
        amount = parseFloat(amount);
        if (!amount) {
            amount = 0;
        }
        if (amount <= 0) {
            toastCenter('转让金额必须大于0元');
            return false;
        }
        if (amount > total_amount) {
            toastCenter('转让金额不能超过最大可转让金额');
            return false;
        }
        if (total_amount >= minAmount) {
            if (amount < minAmount) {
                toastCenter('转让金额必须大于起投金额');
                return false;
            }
            var lastAmount = total_amount - amount;
            if (lastAmount >= minAmount) {
                if (accMod(accSub(amount, minAmount), incAmount) != "0") {
                    toastCenter('金额必须是递增金额整数倍');
                    return false;
                }
            } else {
                if (amount != total_amount) {
                    toastCenter('必须将剩余金额全部投完');
                    return false;
                }
            }
        } else {
            if (amount != total_amount) {
                toastCenter('必须将金额全部投完');
                return false;
            }
        }

        //请求交易系统计算进行计算
        $.ajax({
            type: "get",
            url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/credit-note/calc",
            data: {asset_id:<?= $asset['id']?>, amount: amount, rate: rate},
            dataType: "jsonp"
        });
        return true;
    }

    function callback(data)
    {
        if (data.interest) {
            $('#expect_money').html(data.interest);
        }
        if (data.fee) {
            $('#fee').html(data.fee);
        }
        if (data.realAmount) {
            $('#expect_amount').html(data.realAmount);
        }

        var amount = parseFloat(amount_input.val());
        var rate = parseFloat(discount_rate_input.val());
        var interest = parseFloat(data.interest);
        var maxRate = interest / (amount + interest) * 100;
        maxRate = Math.min(config_rate, maxRate);
        $('#discount_rate_input').attr('placeholder', '不高于' + (parseInt(maxRate * 100) / 100) + '%，可设置2位小数');
        if (rate > maxRate) {
            toastCenter('折让率不能大于'+(parseInt(maxRate * 100) / 100)+'%');
            return false;
        }

        submit_btn.click(function () {
            var rate = parseFloat(discount_rate_input.val());
            if (!rate) {
                rate = 0;
            }
            if (rate > maxRate) {
                toastCenter('折让率不能大于'+(parseInt(maxRate * 100) / 100)+'%');
                return false;
            }
            if (validateData()) {
                 if (rate) {
                     alertReferBox('折让率为' + rate + '%，您确定要发起转让吗？', subConfirm);
                 } else {
                     alertReferBox('您确定要发起转让吗？', subConfirm);
                 }

            }
        });
    }
</script>