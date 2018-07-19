<?php
use yii\helpers\Html;
$this->title = '确认订单';
$this->registerCssFile(ASSETS_BASE_URI .'ctcf/css/credit/creditpay.css?v=1.6');
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.ba-throttle-debounce.min.js?v=161008', ['depends' => \yii\web\JqueryAsset::class]);
?>
<div class="credit-box clearfix">
    <div class="credit-container">
        <div class="credit-container-box">
            <div class="credit-top">
                <p class="credit-top-title lf">【转让】<?= Html::encode($loan->title)?></p>
                <div class="credit-top-tip rg text-align-ct">
                    <input type="checkbox" checked="checked" class="" id="agree"><span>同意并签署</span>
                    <i>"出借协议"</i><a href="/order/order/agreement?pid=<?= $loan->id?>&note_id=<?= $id?>" target="_blank" class="check">查看</a></div>
            </div>
            <div style="clear: both"></div>
            <div class="credit-centent">
                <div class="lf credit-ct-lf">
                    <div class="credit-ct-lf-title">
                        <p class="lf percentage">预期年化收益率<span class="nums"><?= number_format($order->yield_rate * 100, 1) ?>%</span></p>
                        <p class="lf time">剩余期限<span class="nums">
                                <?php
                                $remainingDuration = $loan->getRemainingDuration();
                                if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                                    echo $remainingDuration['months'] . '<span>个月</span>';
                                }
                                if (isset($remainingDuration['days'])) {
                                    if (!isset($remainingDuration['months']) || $remainingDuration['days'] >0) {
                                        echo $remainingDuration['days'] . '<span>天</span>';
                                    }
                                }
                                ?>
                            </span></p>
                        <p class="lf discount">折让率<span class="nums"><?= number_format($note['discountRate'], 2)?>%</span></p>
                    </div>
                    <div

                        class="credit-ct-lf-content">
                        <div class="txt-box"></div>
                    </div>
                </div>
                <div class="rg credit-ct-rg">
                    <p class="sum"><span class="lf-span">出借金额:</span><i class="rg-i text-align-rg"><?= number_format($amount, 2)?></i></p>
                    <p class="reduce"><span class="lf-span">应付利息:</span><i class="rg-i text-align-rg" id="interest">0.00</i></p>
                    <p class="sum"><span class="lf-span">预期收益:</span><i class="rg-i text-align-rg" id="profit">0.00</i></p>
                    <div class="real-credit rg">
                        <p class="real-money"><span>实际支付:</span><i>￥</i><a id="payment"></a></p>
                        <a  class="buy" id="sub_button">确出借买</a>
                    </div>
                    <p style="color: red;    padding-left: 10px;margin-top: 10px;" id="err_message"></p>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
    </div>
</div>
<script>
    function callback(data) {
        $('#interest').html(data.interest);
        $('#profit').html(data.profit);
        $('#payment').html(data.payment);
    }
    $(function () {
        $.ajax({
            type: "get",
            url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/v1/tx/credit-note/calc",
            data: {note_id:<?= $note['id']?>, amount: <?= $amount?>, rate: <?= $rate?>},
            dataType: "jsonp"
        });

        $('#sub_button').bind('click', function () {
            var buy = $(this);
            $('#err_message').hide();
            $('#err_message').html('');
            var _this = $(this);
            if ($('#agree').is(':checked')) {
                if (_this.hasClass('twoFire')) {
                    return false;
                }
                _this.addClass('twoFire');
                var jqXhr = $.post('/credit/order/new', {
                    "_csrf":"<?= Yii::$app->request->csrfToken ?>",
                    "note_id":<?= $note['id'] ?>,
                    "principal":<?= $amount ?>
                }, function (data) {
                    _this.removeClass('twoFire');
                    if (0 !== data.code && '' === data.url) {
                        $('#err_message').show();
                        $('#err_message').html(data.message);
                    }
                    if (data.url) {
                        setTimeout(function () {
                            location.replace(data.url);
                        }, 1000);
                    }
                });
                jqXhr.fail(function () {
                    _this.removeClass('twoFire');
                    $('#err_message').show();
                    $('#err_message').html('系统繁忙，请稍后重试！');
                });
            } else {
                $('#err_message').show();
                $('#err_message').html('您还没有勾选 同意并签署"出借协议"');
            }
        })
    });
</script>

