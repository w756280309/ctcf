<?php
use common\utils\StringUtils;
use frontend\assets\FrontAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = '发起转让';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/usercenter.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/transfer.css?v=20161001', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/transfer.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.ba-throttle-debounce.min.js?v=161008', ['depends' => JqueryAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/login/login_form.js', ['depends' => JqueryAsset::class]);

$action = Yii::$app->controller->action->getUniqueId();
$discountRate = Yii::$app->params['credit']['max_discount_rate'];
$fee = Yii::$app->params['credit']['fee_rate'] * 1000;
$minOrderAmount = bcdiv($asset['minOrderAmount'], 100, 2);
$incrOrderAmount = bcdiv($asset['incrOrderAmount'], 100, 2);
$calcDiscountRate = min($discountRate, bcmul(bcdiv($asset['currentInterest'], bcadd($asset['currentInterest'], $asset['maxTradableAmount'], 14), 14), 100, 2));
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
                <p><?= Html::encode($loan->title)?><a href="/credit/note/rules" target="_blank">转让规则</a></p>
                <div class="contentCenter_ul clearfix_all">
                    <ul class="text_center">
                        <li class="contentCenter_ul_li"><?= floatval($apr) * 100 ?><span>%</span></li>
                        <li>预期年化收益率</li>
                    </ul>
                    <ul class="text_center">
                        <li class="contentCenter_ul_li">
                            <?php
                                $remainingDuration = $loan->getRemainingDuration();
                                if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                                    echo $remainingDuration['months'].'<span>个月</span>';
                                }
                                if (isset($remainingDuration['days'])) {
                                    if (!isset($remainingDuration['months']) || $remainingDuration['days'] >0) {
                                        echo $remainingDuration['days'].'<span>天</span>';
                                    }
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
                                            <img src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao1.png" alt="">
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
                            <span class="common_color"><?= number_format($asset['maxTradableAmount'] / 100, 2) ?></span> 元
                        </li>
                    </ul>
                    <ul class="transferMoney clearfix_all relative">
                        <!--转让金额-->
                        <li class="left transferMoney_title">转让金额：</li>
                        <li class="left transferMoney_space"></li>
                        <li class="left transferMoney_money transfer_common">
                            <input type="text" name="" id="credit_amount" placeholder="起投<?= StringUtils::amountFormat2($minOrderAmount) ?>元，递增<?= StringUtils::amountFormat2($incrOrderAmount) ?>元" autocomplete="off" t_value="" onkeyup="if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}">
                            <span>元</span>
                        </li>
                        <li class="transferMoney_error common_color" id="amount_error">/li>
                    </ul>

                    <ul class="discountRate clearfix_all relative">
                        <li class="left discountRate_title">折让率：</li>
                        <li class="left discountRate_space"></li>
                        <li class="left discountRate_rate transfer_common">
                            <input type="text" name="discount_rate" id="discount_rate_input" value=""  placeholder="不高于<?= $calcDiscountRate ?>%，可设置2位小数" autocomplete="off" maxlength="4" t_value="" onkeyup=" if (this.value) {if (!this.value.match(/^[\+\-]?\d+?\.?\d*?$/)) {if (this.t_value) {this.value = this.t_value;} else {this.value = '';}} else {this.t_value = this.value;}}" >
                            <span>%</span>
                        </li>
                        <li class="discountRate_tip_icon"></li>
                        <li>
                            <div class="discountRate_tip" style="display: none;">
                                <div class="ui-poptip-shadow">
                                    <div class="ui-poptip-container">
                                        <div id="ui-poptip-arrow_add_two" class="ui-poptip-arrow">
                                            <img src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao1.png" alt="">
                                        </div>
                                        <div class="ui-poptip-content" data-role="content">
                                            产品在转让时折让的比率，如折让率1%，则按照转让价值的99%来出售
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="discountRate_error common_color" id="discount_rate_error"></li>
                    </ul>

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
                                            <img src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao1.png" alt="">
                                        </div>
                                        <div class="ui-poptip-content" data-role="content">
                                            转让需要支付转让金额的<?= $fee ?>‰手续费，在成交后直接从成交金额中扣除
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="agreement"><input id="agreement" type="checkbox" checked="checked"> 同意并签署"转让协议" <a href="/order/order/agreement?pid=<?= $loan->id ?>&note_id=1" target="_blank">查看</a></div>
                    <span>
                        <input id="credit_submit_btn" type="button" value="确定转让" class="submit_btn" onclick="validateData()">
                    </span>
                    <div class="agreement-err hide">您还没有勾选同意并签署"转让协议"</div>
                    <div class="risk-note"><a href="/credit/note/risk-note?type=1" target="_blank">查看并确认《风险提示》</a></div>
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
    <div class="confirmBox-top" style="padding: 0px 17px;height:73px;">
        <p></p>
    </div>
    <div class="confirmBox-bottom hide scene_bonus_refer">
        <div onclick="confirmClose();">我知道了</div>
    </div>
    <div class="confirmBox-bottom scene-discount-refer">
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
    var minAmount = <?= floatval($minOrderAmount) ?>;
    var incAmount = <?= floatval($incrOrderAmount) ?>;

    $(function() {
        amount_input.change($.throttle(100,function () {
            validateData();
        }));
        discount_rate_input.change($.throttle(100,function () {
            validateData();
        }));
        amount_input.keyup($.throttle(100, function () {
            calc();
        }));
        discount_rate_input.keyup($.throttle(100, function () {
            calc();
        }));

        $('#agreement').on('click', function () {
            if ('checked' === $(this).attr('checked')) {
                $(this).removeAttr('checked');
            } else {
                $(this).attr('checked', 'checked');
            }
        });

        var alertBonus = '<?= $alertBonus ?>';
        var bonusAmountFormat = '<?= StringUtils::amountFormat2($bonusAmount) ?>';
        if (alertBonus) {
            $('.confirmBox .scene-discount-refer').css('display', 'none');
            $('.confirmBox .scene_bonus_refer').css('display', 'block');
            $('.confirmBox-top').find('p').text('此项目已用加息券'+bonusAmountFormat+'元，将于项目到期后返还到账户。如果部分/全部转让成功，项目加息券加息收益将视为主动放弃。').css('text-align', 'left');
            $('.mask').show();
            $('.confirmBox').show();
        }
    });

    function confirmClose() {
        subClose();
        $('.confirmBox .scene_bonus_refer').css('display', 'none');
        $('.confirmBox .scene-discount-refer').css('display', 'block');
        $('.confirmBox-top').find('p').text('');
    }

    function subConfirm(obj)
    {
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
                    if ('请登录' === err['msg']) {
                        login();
                        return;
                    }
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
        xhr.always(function () {
            btn.removeClass('twoClick');
        });
        xhr.fail(function () {
            btn.removeClass('twoClick');
            alert('系统繁忙，请稍后重试！');
        });
    }
    
    function calc() {
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        var amount = parseFloat(amount_input.val());
        if (!amount) {
            amount = 0;
        }
        $.ajax({
            type: "get",
            url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/v1/tx/credit-note/calc",
            data: {asset_id:<?= $asset['id']?>, amount: amount, rate: rate},
            dataType: "jsonp"
        });
    }

    function validateData()
    {
        var rate = parseFloat(discount_rate_input.val());
        if (!rate) {
            rate = 0;
        }
        if (rate < 0) {
            discount_rate_error.html('折让率不能小于0');
            discount_rate_error.show();
            return false;
        }

        var amount = amount_input.val();
        if ('' === amount) {
            amount_error.html('请输入转让金额');
            amount_error.show();
            return false;
        }
        amount = parseFloat(amount);
        if (!amount) {
            amount = 0;
        }
        if (amount <= 0) {
            amount_error.html('转让金额必须大于0元');
            amount_error.show();
            return false;
        }
        if (amount > total_amount) {
            amount_error.html('转让金额不能超过最大可转让金额');
            amount_error.show();
            return false;
        }
        if (total_amount >= minAmount) {
            if (amount < minAmount) {
                amount_error.html('转让金额必须大于起投金额');
                amount_error.show();
                return false;
            }
            var lastAmount = total_amount - amount;
            if (lastAmount >= minAmount) {
                if (accMod(accSub(amount, minAmount), incAmount) != "0") {
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
        //请求交易系统计算进行计算
        $.ajax({
            type: "get",
            url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/v1/tx/credit-note/calc",
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
            discount_rate_error.html('折让率不能大于'+(parseInt(maxRate * 100) / 100)+'%');
            discount_rate_error.show();
            return false;
        }

        submit_btn.click(function () {
            if ('checked' !== $('#agreement').attr('checked')) {
                $('.agreement-err').removeClass('hide');
                return false;
            }

            if (!$('.agreement-err').hasClass('hide')) {
                $('.agreement-err').addClass('hide');
            }

            discount_rate_error.hide();

            var rate = parseFloat(discount_rate_input.val());
            if (!rate) {
                rate = 0;
            }
            if (rate > maxRate) {
                discount_rate_error.html('折让率不能大于'+(parseInt(maxRate * 100) / 100)+'%');
                discount_rate_error.show();
                return false;
            }
            if (validateData()) {
                discount_rate_error.hide();
                $('.mask').show();
                $('.confirmBox').show();
                $('.confirmBox .scene-discount-refer').show();
                if (rate == 0) {
                    $('.confirmBox-top').css('line-height', '73px').find('p').text('您确定要发布转让吗？').css('text-align', 'center');
                } else {
                    $('.confirmBox-top').css('line-height', '73px').find('p').text('折让率为' + rate + '%，您确定要发布转让吗？').css('text-align', 'center');
                }
            }
        });
    }
</script>