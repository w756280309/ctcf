<?php
use yii\helpers\Html;
$this->title = '发起转让';

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/usercenter.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/transfer.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/transfer.js', ['depends' => \yii\web\JqueryAsset::class]);
$action = Yii::$app->controller->action->getUniqueId();
$minOrderAmount = Yii::$app->params['credit_trade']['min_order_amount'];
$incrOrderAmount = Yii::$app->params['credit_trade']['incr_order_amount'];
$discountRate = Yii::$app->params['credit_trade']['max_discount_rate'];
$fee = Yii::$app->params['credit_trade']['fee_rate'] * 1000;
?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->renderFile('@frontend/views/left.php') ?>
        </div>
        <div class="rightcontent">
            <p class="title">
                <i></i>
                <span>我的转让</span>
            </p>
            <div class="contentCenter">
                <p><?= Html::encode($loan->title)?></p>
                <div class="contentCenter_ul clearfix_all">
                    <ul class="text_center">
                        <li class="contentCenter_ul_li"><?= floatval($apr) * 100 ?><span>%</span></li>
                        <li>预期年化收益率</li>
                    </ul>
                    <ul class="text_center">
                        <li class="contentCenter_ul_li">
                            <?php
                            $remainingDuration = $loan->getRemainingDuration();
                            if (isset($remainingDuration['months'])) {
                                echo $remainingDuration['months'] . '<span>个月</span>';
                            }
                            if (isset($remainingDuration['days'])) {
                                echo $remainingDuration['days'] . '<span>天</span>';
                            }
                            ?>
                            </li>
                        <li>剩余期限</li>
                    </ul>
                    <ul class="text_center">
                        <li class="contentCenter_ul_li"><?= number_format($asset['remainingInterest'] / 100, 2)?><span>元</span></li>
                        <li>预期总收益</li>
                    </ul>
                    <ul class="text_center">
                        <li class="contentCenter_ul_li"><?= number_format($asset['currentInterest'] / 100, 2)?><span>元</span></li>
                        <li>当期持有期利息<a id="current_interest"></a></li>
                        <li>
                            <div class="contentCenter_tip" style="display: none;">
                                <div class="ui-poptip-shadow">
                                    <div class="ui-poptip-container">
                                        <div id="ui-poptip-arrow_add_one" class="ui-poptip-arrow">
                                            <img src="../../images/useraccount/diglog-jiao1.png" alt="">
                                        </div>
                                        <div class="ui-poptip-content" data-role="content">
                                            在卖出转让前，剩余本金自上一期回款后所产生的利息
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="contentBottom">
                <div class="contentBottom_box">
                    <ul class="availableAssets clearfix_all">
                        <!--转让的最大金额-->
                        <li class="left availableAssets_title">可转让金额：</li>
                        <li class="left availableAssets_space"></li>
                        <li class="left availableAssets_money">
                            <span class="common_color"><?= number_format($asset['maxTradableAmount'] / 100, 2)?></span> 元
                        </li>
                    </ul>
                    <ul class="transferMoney clearfix_all relative">
                        <!--转让金额-->
                        <li class="left transferMoney_title">转让金额：</li>
                        <li class="left transferMoney_space"></li>
                        <li class="left transferMoney_money transfer_common">
                            <input type="text" name="" id="credit_amount" placeholder="起投<?= number_format($minOrderAmount) ?>元，递增<?= number_format($incrOrderAmount) ?>元" autocomplete="off" t_value="" onkeyup="if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}">
                            <span>元</span>
                        </li>
                        <li class="transferMoney_error common_color" id="amount_error">/li>
                    </ul>
                    <!--<div style="color: red" id="error_fen"></div>-->

                    <ul class="discountRate clearfix_all relative">
                        <li class="left discountRate_title">折让率：</li>
                        <li class="left discountRate_space"></li>
                        <li class="left discountRate_rate transfer_common">
                            <input type="text" name="discount_rate" id="discount_rate_input" value=""  placeholder="不高于<?= $discountRate ?>%，可设置2位小数" autocomplete="off" maxlength="4" t_value="" onkeyup=" if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}" >
                            <span>%</span>
                        </li>
                        <li class="discountRate_tip_icon"></li>
                        <li>
                            <div class="discountRate_tip" style="display: none;">
                                <div class="ui-poptip-shadow">
                                    <div class="ui-poptip-container">
                                        <div id="ui-poptip-arrow_add_two" class="ui-poptip-arrow">
                                            <img src="../../images/useraccount/diglog-jiao1.png" alt="">
                                        </div>
                                        <div class="ui-poptip-content" data-role="content">
                                            产品在转让时 折让的比率，如折让率1%，则按照转让价值的99%来出售
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="discountRate_error common_color" id="discount_rate_error"></li>
                    </ul>
                    <!--<div style="color: red" id="error_fen_discounts"></div>-->

                    <ul class="canObtain clearfix_all">
                        <li class="left canObtain_title">应收利息：</li>
                        <li class="left canObtain_space"></li>
                        <li class="left canObtain_money"><span class="common_color" id="expect_money">0.00</span>&nbsp;&nbsp;元</li>
                    </ul>

                    <ul class="discountObtain clearfix_all">
                        <li class="left discountObtain_title">折让后价格：</li>
                        <li class="left discountObtain_space"></li>
                        <li class="left discountObtain_money"><span class="common_color" id="expect_amount">0.00</span>&nbsp;&nbsp;元</li>
                    </ul>

                    <ul class="poundage clearfix_all relative">
                        <li class="left poundage_title">手续费：</li>
                        <li class="left poundage_space"></li>
                        <li class="left poundage_money"><span class="common_color" id="fee">0.00</span>&nbsp;&nbsp;元</li>
                        <li class="poundage_tip_icon"></li>
                        <li>
                            <div class="poundage_tip" style="display: none;">
                                <div class="ui-poptip-shadow">
                                    <div class="ui-poptip-container">
                                        <div id="ui-poptip-arrow_add_two" class="ui-poptip-arrow">
                                            <img src="../../images/useraccount/diglog-jiao1.png" alt="">
                                        </div>
                                        <div class="ui-poptip-content" data-role="content">
                                            转让需要支付转让金额的<?= $fee ?>‰手续费，在成交后直接从成交金额中扣除
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>

                    <span class="">
                            <input id="credit_submit_btn" type="button" name="" value="确定转让" class="submit_btn" onclick="">
                            <input type="hidden" name="" id="" value="">
                        </span>
                </div>

            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<!--mask弹框-->
<div class="mask"></div>

<!--确认弹框-->
<div class="confirmBox">
    <div class="confirmBox-title">提示</div>
    <div class="confirmBox-top">
        <p></p>
    </div>
    <div class="confirmBox-bottom">
        <div class="confirmBox-left" onclick="subClose()">关闭</div>
        <div class="confirmBox-right" onclick="subConfirm(this)">确认</div>
    </div>
</div>
<script>
    var submit_btn = $('#credit_submit_btn');
    var amount_input = $('#credit_amount');
    var discount_rate_input = $('#discount_rate_input');
    var current_interest = <?= floatval($asset['currentInterest'] / 100)?>;
    var total_amount = <?= floatval($asset['maxTradableAmount'] / 100)?>;
    var amount_error = $('#amount_error');
    var discount_rate_error = $('#discount_rate_error');
    var config_rate = <?= floatval($discountRate) ?>;
    var minAmount = <?= floatval($minOrderAmount)?>;
    var incAmount = <?= floatval($incrOrderAmount)?>

    amount_input.change(function () {
        validateData();
    });
    discount_rate_input.change(function () {
        validateData();
    });

    submit_btn.click(function () {
        discount_rate_error.hide();
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        if (validateData()){
            discount_rate_error.hide();
            $('.mask').show();
            $('.confirmBox').show();
            if (rate == 0) {
                $('.confirmBox-top').find('p').text('您确定要发布转让吗？');
            } else {
                $('.confirmBox-top').find('p').text('折让率为' + rate + '%，您确定要发布转让吗？');
            }
        }
    });

    function subConfirm(obj) {
        $('.mask').hide();
        $('.confirmBox').hide();
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        var amount = parseFloat(amount_input.val());
        if (!amount) {
            amount = 0;
        }
        var btn = $(obj);
        if (btn.hasClass('twoClick')) {
            return false;
        }
        btn.addClass('twoClick');
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
                        amount_error.html(err['msg']);
                        amount_error.show();
                    } else if (err['attribute'] === 'discountRate') {
                        discount_rate_error.html(err['msg']);
                        discount_rate_error.show();
                    } else if (err['attribute'] === '' && err['msg']) {
                        amount_error.html(err['msg']);
                        amount_error.show();
                    }
                }
            } else if (data['code'] === 0) {
                window.location.href = '/info/success?source=credit_new&jumpUrl=/credit/trade/assets?type=2'
            }
        });
        xhr.always(function() {
            btn.removeClass('twoClick');
        });
        xhr.fail(function() {
            btn.removeClass('twoClick');
            alert('系统繁忙，请稍后重试！');
        });
    }

    function validateData() {
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        if (rate < 0) {
            discount_rate_error.html('折让率不能小于0');
            discount_rate_error.show();
            return false;
        }
        if (rate > config_rate) {
            discount_rate_error.html('折让率不能大于' + config_rate + '%');
            discount_rate_error.show();
            return false;
        }
        var amount = parseFloat(amount_input.val());
        if (!amount) {
            amount = 0;
        }
        if (amount <= 0) {
            amount_error.html('转让金额必须大于0元');
            amount_error.show();
            return false;
        }
        if (amount < minAmount) {
            amount_error.html('转让金额必须大于起投金额');
            amount_error.show();
            return false;
        }
        if (amount > total_amount) {
            amount_error.html('转让金额不能超过最大可转让金额');
            amount_error.show();
            return false;
        }
        if (total_amount >= minAmount) {
            var lastAmount = total_amount - minAmount;
            if (lastAmount >= minAmount) {
                if ((amount - minAmount) % incAmount != 0) {
                    amount_error.html('金额必须是递增金额整数倍');
                    amount_error.show();
                    return false;
                }
            } else {
                if (amount != total_amount) {
                    amount_error.html('必须将剩余金额全部投完');
                    amount_error.show();
                    return false;
                }
            }
        } else {
            if (amount != total_amount) {
                amount_error.html('必须将金额全部投完');
                amount_error.show();
                return false;
            }
        }

        amount_error.hide();
        discount_rate_error.hide();
        amount_error.hide();
        $('#expect_money').html(parseInt(amount / total_amount * current_interest * 100) / 100);
        $('#fee').html(parseInt(amount * 0.003 * 100) / 100);
        $('#expect_amount').html(parseInt(amount * (1 - rate / 100) * 100) / 100);
        return true;
    }
</script>