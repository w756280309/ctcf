<?php

use common\utils\StringUtils;

$this->title = '可用代金券';
$replaceUrl = Yii::$app->request->referrer;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/multiple-coupon/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script type="text/javascript">
    var url = '/user/coupon/valid?sn=<?= $sn ?>&money=<?= $money ?>';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="flex-content">
    <div class="topTitle f18">
        <img class="goback lf" src="<?= FE_BASE_URI ?>wap/multiple-coupon/images/back.png" alt="" onclick="location.replace('<?= $replaceUrl ?>')">
        选择代金券
    </div>

    <?php if (!empty($selectedCoupon)) : ?>
        <p class="coupon-remind coupon-remind1">已选<span class="coupon-remind-number"><?= $couponCount ?></span>张代金券，可抵扣<span
            class="coupon-remind-sum"><?= StringUtils::amountFormat2($couponMoney) ?></span>元投资</p>
    <?php else : ?>
        <p class="coupon-remind coupon-remind1">可选多张代金券</p>
    <?php endif; ?>

    <ul class="coupon-list">
        <?= $this->render('_valid_list', ['coupons' => $coupons, 'sn' => $sn, 'selectedCoupon' => $selectedCoupon]) ?>
        <div class="load"></div>
    </ul>
    <a href="javascript:void(0)" onclick="location.replace('<?= $replaceUrl ?>')" class="coupon-button">
        <p class="btn-line1">确认选择</p>
        <p class="btn-line2">（已选<?= $couponCount ?>张，可抵扣<?= StringUtils::amountFormat2($couponMoney) ?>元投资）</p>
    </a>
</div>

<script type="text/javascript">
    var allowClick = true;

    $('.coupon-list').on('click', '.coupons', function (e) {
        e.preventDefault;

        if(!allowClick) {
            return false;
        }

        var $this = $(this);
        var sn = $this.attr('sn');
        var userCouponId = $this.attr('userCouponId');
        var opt = 'selected';
        var money = '<?= $money ?>';

        if ($this.hasClass('coupons-on')) {
            opt = 'canceled';
        }

        var xhr = $.get('/user/coupon/add-coupon-session', {sn: sn, couponId: userCouponId, money: money, opt: opt});
        allowClick = false;

        xhr.done(function(data) {
            if (0 === data.code) {
                if ($this.hasClass('coupons-on')) {
                    $this.removeClass('coupons-on').addClass('coupons-off');
                } else {
                    $this.removeClass('coupons-off').addClass('coupons-on');
                }

                var total = data.data.total;
                var money = WDJF.numberFormat(data.data.money, true);
                var msg = '';

                $('p.btn-line2').html('（已选'+total+'张，可抵扣'+money+'元投资）');

                if (total > 0) {
                    msg = '已选<span class="coupon-remind-number">'+total+'</span>张代金券，可抵扣<span class="coupon-remind-sum">'+money+'</span>元投资';
                } else {
                    msg = '可选多张代金券';
                }

                $('p.coupon-remind').html(msg);
                allowClick = true;
            }
        });

        xhr.fail(function(jqXHR) {
            if (400 === jqXHR.status && jqXHR.responseText) {
                var resp = $.parseJSON(jqXHR.responseText);
                if (1 === resp.code) {
                    toastCenter(resp.message, function () {
                        allowClick = true;
                    });
                }
            } else {
                toastCenter('系统繁忙，请稍后重试！', function () {
                    allowClick = true;
                });
            }
        });
    });
</script>
