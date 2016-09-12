<?php
$this->title="购买";

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
JS
    , 1);

$this->registerJsFile(ASSETS_BASE_URI.'js/order.js?v=20160908', ['depends' => 'yii\web\YiiAsset']);
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160518">

<!--   购买页 start-->
    <div class="row produce">
        <div class="col-xs-12 text-align-lf first-hang" style="padding-right: 0;"><?=$deal->title?></div>
        <div class="col-xs-4 text-align-ct">预期年化收益</div>
        <div class="col-xs-8 text-align-lf col"><?=  ($deal->yield_rate*100)?>%</div>
        <div class="col-xs-4 text-align-ct">项目</div>
        <div class="col-xs-8 text-align-lf col">
            <?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><?= $ex['unit']?>
            <?php if (!empty($deal->kuanxianqi)) { ?>(含宽限期<?=$deal->kuanxianqi?>天)<?php } ?></div>
        <div class="col-xs-4 text-align-ct">可投余额</div>
        <div class="col-xs-8 text-align-lf col"><?=  number_format($param['order_balance'], 2)?>元</div>
    </div>
    <div class="row surplus margin-top">
        <div class="col-xs-4 text-align-ct">可用金额</div>
        <div class="col-xs-5 safe-lf text-align-lf"><?=  number_format($param['my_balance'], 2)?>元</div>
        <div class="col-xs-3 safe-txt text-align-ct"><a href="/user/userbank/recharge?from=<?= urlencode('/order/order?sn='.$deal->sn)?>">去充值</a></div>
    </div>
    <form action="/order/order/doorder?sn=<?= $deal->sn ?>" method="post" id="orderform" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
        <div class="row sm-height border-bottom">
            <div class="col-xs-4 safe-txt text-align-ct">投资金额</div>
            <input name="money" type="number" id="money" value="<?= empty($money) ? '' : $money ?>" placeholder="请输入投资金额"  class="col-xs-6 safe-lf text-align-lf" step="<?=$deal->dizeng_money ?>">
            <div class="col-xs-2 safe-txt">元</div>
        </div>

        <div class="row sm-height border-bottom">
            <?php if (empty($couponId)) { ?>
                <div class="col-xs-4 safe-txt text-align-ct">使用代金券</div>
                <?php if (empty($coupon)) { ?>
                <div class="col-xs-8 safe-txt">无可用</div>
                <?php } else { ?>
                <div class="col-xs-8 safe-txt" onclick="toCoupon()"><span class="notice">请选择</span></div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-xs-4 safe-txt text-align-ct">代金券抵扣</div>
                <div class="col-xs-5 safe-txt" onclick="toCoupon()"><?= $coupon->couponType->amount ?>元</div>
                <div class="col-xs-3 safe-txt text-align-ct" onclick="resetCoupon()">清除</div>
                <input name="couponId" id="couponId" type="text" value="<?= $coupon->id ?>" hidden="hidden">
                <input name="couponMoney" id="couponMoney" type="text" value="<?= $coupon->couponType->amount ?>" hidden="hidden">
            <?php } ?>
        </div>

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

    <!-- 遮罩层 start  -->
    <div class="mask"></div>
    <!-- 遮罩层 end  -->
    <!-- 输入弹出框 start  -->
    <div class="succeed-info hidden">
        <div class="col-xs-12"><img src="<?= ASSETS_BASE_URI ?>images/succeed.png" alt="对钩"> </div>
        <div class="col-xs-12">购买成功</div>
    </div>
    <!-- 输入弹出框 end  -->
    <!-- 购买页 end  -->

    <script type="text/javascript">
        function toCoupon()
        {
            var money = $('#money').val();
            var url = '/user/coupon/valid?sn=<?= $deal->sn ?>&money='+money;

            location.href = url;
        }

        function resetCoupon()
        {
            var money = $('#money').val();
            var url = '/order/order?sn=<?= $deal->sn ?>&money='+money;

            location.replace(url);
        }
    </script>
