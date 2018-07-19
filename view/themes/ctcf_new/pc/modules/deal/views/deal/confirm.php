<?php

use common\utils\StringUtils;
use common\view\LoanHelper;

$this->title = '确认订单';
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/deal/buy.css?v=20161113');

?>
<!-- invest BUY start-->
<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box">
            <div class="invest-top">
                <p class="invest-top-title lf"><?= $deal->title ?></p>
                <div class="invest-top-tip rg text-align-rg">
                    <input type="checkbox" checked="checked" class="" id="agree"/><span> 同意并签署</span>
                    <i>"产品合同"</i><a href="/order/order/agreement?pid=<?= $deal->id ?>" class="check" target="_blank">查看</a></div>
            </div>
            <div style="clear: both"></div>
            <div class="invest-centent">
                <div class="lf invest-ct-lf">
                    <div class="invest-ct-lf-title">
                        <p class="lf percentage">
                            借贷双方约定利率
                            <span class="nums"> <?= LoanHelper::getDealRate($deal) ?><i>%</i>
                                <?php if (!empty($deal->jiaxi)) { ?>
                                    <span class="other-nums">
                                        +<?= StringUtils::amountFormat2($deal->jiaxi) ?>%
                                    </span>
                                <?php } ?>
                            </span>
                        </p>
                        <p class="rg time">项目期限<span class="nums"><?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><?= $ex['unit']?></span></p>
                    </div>
                    <div class="invest-ct-lf-content">
                        <div class="txt-box"></div>
                    </div>
                </div>
                <div class="rg invest-ct-rg">
                    <p class="sum"><span class="lf-span">出借金额:</span><i class="rg-i text-align-rg"><?= number_format($money,2)?>元</i></p>
                    <p class="reduce"><span class="lf-span">代金券抵扣:</span><i class="rg-i text-align-rg"><?= $cou_money ? '-' : '' ?><?= number_format($cou_money ,0) ?>元</i></p>
                    <div class="real-invest rg">
                        <p class="real-money"><span>实际支付:</span><i>￥</i><?= number_format(max($money-$cou_money, 0), 2)?></p>
                        <p><a class="buy" id="sub_button">确认出借</a></p>
                    </div>
                    <div style="clear: both"></div>
                    <p style="color: red;    padding-left: 10px;margin-top: 10px;" id="err_message"></p>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
    </div>
</div>
<!-- invest BUY end-->
<script>
    $(function () {
        var allowSub = true;
        $('#sub_button').on('click', function () {
            var buy = $(this);
            if ($('#agree').is(':checked')) {
                if (!allowSub) {
                    return;
                }

                allowSub = false;
                buy.html("购买中……");
                var xhr = $.post('/order/order/doorder?sn=<?= $sn ?>', {
                    'money': <?= $money ?>,
                    '_csrf': '<?= Yii::$app->request->csrfToken ?>'
                }, function (data) {
                    if (data.code == 0) {
                        $('#err_message').hide();
                        location.replace(data.tourl);
                        //toast('投标成功');
                    } else {
                        $('#err_message').show();
                        $('#err_message').html(data.message);
                        allowSub = true;
                    }
                    if (data.tourl != undefined) {
                        $('#err_message').hide();
                        setTimeout(function () {
                            location.replace(data.tourl);
                        }, 1000);
                    }
                });
                xhr.fail(function(){
                    window.location.reload()
                });
                xhr.always(function () {
                    allowSub = true;
                    buy.html("确出借买");
                })
            } else {
                $('#err_message').show();
                $('#err_message').html('您还没有勾选 同意并签署"产品合同"');
            }
        })
    });
</script>
