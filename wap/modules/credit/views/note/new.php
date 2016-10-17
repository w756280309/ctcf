<?php

use common\utils\StringUtils;
use yii\helpers\Html;

$this->title = '转让';

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/transfer_order.css', ['depends' => 'wap\assets\WapAsset']);

$discountRate = Yii::$app->params['credit_trade']['max_discount_rate'];
$fee = Yii::$app->params['credit_trade']['fee_rate'] * 1000;
?>

<div class="row produce">
    <div class="col-xs-11 col-xs-offset-1 text-align-lf first-line" style="padding-right: 0;"><?= Html::encode($loan->title)?></div>
    <div class="col-xs-3 col-xs-offset-1">可转让金额</div>
    <div class="col-xs-8 text-align-lf col"><?= number_format($asset['maxTradableAmount'] / 100, 2)?>元</div>
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
    <input name="money" type="text" id="money" autocomplete="off" value="" t_value="" placeholder="起投<?= StringUtils::amountFormat2(bcdiv($asset['minOrderAmount'], 100, 2)) ?>元，递增<?= StringUtils::amountFormat2(bcdiv($asset['incrOrderAmount'], 100, 2)) ?>元" class="col-xs-6 safe-lf text-align-lf font-26" onkeyup=" if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {this.value = this.t_value;}else{this.t_value = this.value;}}">
    <div class="col-xs-2 safe-txt font-32 money_yuan">元</div>
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
    <input name="rate" type="text" id="rate" autocomplete="off" maxlength="4" value="" t_value="" placeholder="不高于<?= $discountRate ?>%，可设置2位小数"  class="col-xs-6 safe-lf text-align-lf font-26" onkeyup=" if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {this.value = this.t_value;}else{this.t_value = this.value;}}">
    <div class="col-xs-2 safe-txt font-32 money_yuan">%</div>
</div>
<div class="hide toRefer"></div>
<div class="row shouyi shouyi_space">
    <div class="col-xs-3 col-xs-offset-1 safe-lf">应付利息</div>
    <div class="col-xs-8 safe-lf common_color"><span id="expect_money">0.00元</span></div>
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
</div>
<!--tips-->
<p class="col-xs-12 shouyi_tips">
    <img class="shouyi_tips_tra" src="<?= ASSETS_BASE_URI ?>images/credit/triangle.png" alt="">
    转让需要支付转让金额的<?= $fee ?>‰手续费，在成交后直接从成交金额中扣除。
</p>
<!--bottom-->
<div class="row login-sign-btn ht">
    <div class="col-xs-6 col-xs-offset-3 text-align-ct">
        <input id="buybtn" class="btn-common btn-normal" type="button" value="确定转让">
    </div>
    <div class="col-xs-3 empty_div"></div>
</div>
<div class="row surplus">
    <div class="col-xs-12 text-align-ct bottom_center"><a href="/credit/note/rules">转让规则</a></div>
</div>
<script>
    $(function(){
        //提示信息的显示与隐藏
        var flag_shouyi = 0;
        var flag_sm= 0;
        $('.shouyi_tips_img').bind('click',function(e){
            var e = event || window.event;
            if(!flag_shouyi){
                $('.shouyi_tips').show();
                flag_shouyi = 1;
            }
            e.stopPropagation();
        })
        $('body').bind('click',function(e){
            if(flag_shouyi){
                $('.shouyi_tips').hide();
                flag_shouyi = 0;
            }
        })

        $('.sm-height_tips_img').bind('click',function(e){
            var e = event || window.event;
            if(!flag_sm){
                $('.sm-height_tips').show();
                flag_sm = 1;
            }
            e.stopPropagation();
        })
        $('body').bind('click',function(e){
            if(flag_sm){
                $('.sm-height_tips').hide();
                flag_sm = 0;
            }
        })

        var discount_rate_input = $('#rate');
        var amount_input = $('#money');
        var total_amount = <?= floatval($asset['maxTradableAmount'] / 100)?>;
        var current_interest = <?= floatval($asset['currentInterest'] / 100)?>;
        var config_rate = <?= floatval($discountRate) ?>;
        var feeRate = <?= floatval($fee / 1000) ?>;
        amount_input.change(function () {
            validateData();
        });
        discount_rate_input.change(function () {
            validateData();
        });
        function validateData() {
            var rate = parseFloat(discount_rate_input.val());
            if (!rate) {
                rate = 0;
            }
            var amount = parseFloat(amount_input.val());
            if (!amount) {
                amount = 0;
            }
            if (amount <= 0) {
                torefer('转让金额必须大于0元');
                return false;
            }
            if (rate < 0) {
                torefer('折让率不能小于0');
                return false;
            }
            if (rate > config_rate) {
                torefer('折让率不能大于' + config_rate + '%');
                return false;
            }
            if (amount > 0) {
                var interest = parseInt(amount / total_amount * current_interest * 100) / 100;
                $('#expect_money').html(interest);
                $('#fee').html(parseInt(amount * feeRate * 100) / 100);
                $('#expect_amount').html(parseInt((amount + interest) * (1 - rate / 100) * 100) / 100);
            }
            return true;
        }
        $('#buybtn').bind('click',function () {
            if (!validateData()) {
                return false;
            }
            var rate = parseFloat(discount_rate_input.val());
            if (!rate) {
                rate = 0;
            }
            var amount = parseFloat(amount_input.val());
            if (!amount) {
                amount = 0;
            }
            var _this = $(this);
            if (_this.hasClass('twoClick')) {
                return false;
            }
            _this.addClass('twoClick');
            var xhr = $.post('/credit/note/create', {
                '_csrf': '<?= Yii::$app->request->csrfToken?>',
                'asset_id': '<?= $asset['id']?>',
                'amount': amount,
                'rate': rate
            }, function (data) {
                _this.removeClass('twoClick');
                var res = data['data'];
                if (data['code'] === 1) {
                    var arr = new Array();
                    var brr = new Array();
                    var crr = new Array();
                    for (var i = 0; i < res.length; i++) {
                        var err = res[i];
                        if (err['attribute'] === 'amount') {
                            arr.push(err['msg']);
                        } else if (err['attribute'] === 'discountRate') {
                            brr.push(err['msg']);
                        } else if (err['attribute'] === '' && err['msg']) {
                            crr.push(err['msg']);
                        }
                    }
                    if (arr.length > 0) {
                        torefer(arr[0]);
                        return false;
                    } else if (brr.length > 0) {
                        torefer(brr[0]);
                        return false;
                    } else if (crr.length > 0) {
                        torefer(crr[0]);
                        return false;
                    }
                } else if (data['code'] === 0) {
                    window.location.href = '/credit/note/detail?id=' + data['data']['id'];
                }
            });
            xhr.always(function() {
                _this.removeClass('twoClick');
            });
            xhr.fail(function() {
                _this.removeClass('twoClick');
                torefer('系统繁忙，请稍后重试！');
            });
        });
    });

    function torefer(val)
    {
        var $alert = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>');
        $alert.insertAfter($('.toRefer'));
        $alert.find('div').width($alert.width());
        setTimeout(function () {
            $alert.fadeOut();
            setTimeout(function () {
                $alert.remove();
            }, 200);
        }, 2000);
    }

</script>