<?php

$this->title = '购买';

use common\utils\StringUtils;
use common\view\LoanHelper;
use wap\assets\WapAsset;
use yii\web\YiiAsset;

$validCouponCount = count($coupons);

$yr = $deal->yield_rate;
$qixian = $deal->getDuration()['value'];
$retmet = $deal->refund_method;
$sn = $deal->sn;
$isFlexRate = $deal->isFlexRate;
$this->registerJs(<<<JS
    var yr = "$yr";
    var qixian = "$qixian";
    var retmet = "$retmet";
    var sn = "$sn";
    var isFlexRate = Boolean("$isFlexRate");
    var validCouponCount = $validCouponCount;
JS
    , 1);

$this->registerJsFile(ASSETS_BASE_URI.'js/order.js?v=20170125', ['depends' => YiiAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/setting.css?v=20170103', ['depends' => WapAsset::class]);

?>

<!--   购买页 start-->
<div class="row produce">
    <div class="col-xs-12 text-align-lf first-hang"><?=$deal->title?></div>
    <div class="col-xs-4 text-align-ct">预期年化收益</div>
    <div class="col-xs-8 text-align-lf col"><?= LoanHelper::getDealRate($deal) ?>%<?php if (!empty($deal->jiaxi)) { ?>+<?= $deal->jiaxi ?>%<?php } ?></div>
    <div class="col-xs-4 text-align-ct">项目</div>
    <div class="col-xs-8 text-align-lf col">
        <?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><?= $ex['unit'] ?>
        <?php if (!empty($deal->kuanxianqi)) { ?>(含宽限期<?=$deal->kuanxianqi?>天)<?php } ?></div>
    <div class="col-xs-4 text-align-ct">可投余额</div>
    <div class="col-xs-8 text-align-lf col"><?= StringUtils::amountFormat3($deal->getLoanBalance()) ?>元</div>
</div>
<div class="row surplus margin-top">
    <div class="col-xs-4 text-align-ct">可用金额</div>
    <div class="col-xs-5 safe-lf text-align-lf"><?= StringUtils::amountFormat3($user->lendAccount->available_balance) ?>元</div>
    <div class="col-xs-3 safe-txt text-align-ct"><a href="/user/userbank/recharge?from=<?= urlencode('/order/order?sn='.$deal->sn)?>">去充值</a></div>
</div>
<form action="/order/order/doorder?sn=<?= $deal->sn ?>" method="post" id="orderform" data-to="1">
    <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
    <div class="row sm-height border-bottom">
        <div class="col-xs-4 safe-txt text-align-ct">投资金额</div>
        <input name="money" type="number" id="money" value="<?= empty($money) ? '' : $money ?>" placeholder="请输入投资金额"  class="col-xs-6 safe-lf text-align-lf" step="any">
        <div class="col-xs-2 safe-txt">元</div>
    </div>

    <?php if ($deal->allowUseCoupon) { ?>
        <input name="couponConfirm" id="couponConfirm" type="text" value="" hidden="hidden">
        <div class="row sm-height border-bottom" id="coupon">
            <?php if ($validCouponCount) { ?>
                <?php
                    $coupon = isset($coupons[$userCouponId]) ? $coupons[$userCouponId] : current($coupons);
                    echo $this->renderFile('@app/modules/user/views/coupon/_valid_coupon.php', ['coupon' => $coupon]);
                ?>
            <?php } else { ?>
                <div class="col-xs-4 safe-txt text-align-ct">使用代金券</div>
                <div class="col-xs-8 safe-txt">无可用</div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="row shouyi">
        <div class="col-xs-4 safe-lf text-align-ct">实际支付</div>
        <div class="col-xs-8 safe-lf text-align-lf shijizhifu">0.00元</div>
    </div>
    <div class="row shouyi">
        <div class="col-xs-4 safe-lf text-align-ct">预计收益</div>
        <div class="col-xs-8 safe-lf text-align-lf yuqishouyi">0.00元</div>
    </div>

    <div class="row login-sign-btn ht">
        <div class="col-xs-3"></div>
        <div class="col-xs-6 text-align-ct">
            <input id="buybtn" class="btn-common btn-normal" type="submit" style="background: #F2F2F2;" value="购买">
        </div>
        <div class="col-xs-3"></div>
    </div>
    <div class="row surplus">
        <center><a class="col-xs-12" href="/order/order/agreement?id=<?= $deal->id ?>">查看“产品合同”</a></center>
    </div>
</form>

<div id="mask" class="mask"></div>
<div id="info" class="bing-info">
    <div class="bing-tishi">提示</div>
    <p></p>
    <div class="bind-btn">
        <span class="bind-xian x-cancel">取消</span>
        <span class="x-confirm">确定</span>
    </div>
</div>

<?php if ($deal->allowUseCoupon && $validCouponCount) { ?>
    <script type="text/javascript">
        function toCoupon() {
            var money = $('#money').val();
            location.replace('/user/coupon/valid?sn=<?= $deal->sn ?>&money='+money);
        }

        function resetCoupon() {
            var html = '<div class="col-xs-4 safe-txt text-align-ct">使用代金券</div><div class="col-xs-8 safe-txt" onclick="toCoupon()"><span class="notice">请选择</span></div>';
            $('#coupon').html(html);

            profit($('#money'));
        }

        $(function () {
            $('#money').on('keyup', function () {
                var money = $('#money').val();

                $.get('/user/coupon/valid-for-loan?sn=<?= $deal->sn ?>&money='+money, function (data) {
                    $('#coupon').html(data);
                });
            });
        })
    </script>
<?php } ?>