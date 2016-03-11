<?php
$this->title="购买";

$this->registerJs('var yr='.$deal->yield_rate, 1);
$this->registerJs('var qixian='.$deal->expires, 1);

$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile('/js/order.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/base.css">
<link rel="stylesheet" href="/css/setting.css">

<!--   购买页 start-->

    <div class="row produce">
        <div class="col-xs-12 text-align-lf first-hang" style="padding-right: 0;"><?=$deal->title?></div>
        <div class="col-xs-4 text-align-ct">年化收益</div>
        <div class="col-xs-8 text-align-lf col"><?=  ($deal->objBaseRate*100)?>%</div>
        <div class="col-xs-4 text-align-ct">项目期限</div>
        <div class="col-xs-8 text-align-lf col"><?= $deal->expires ?>天<?php if (!empty($deal['kuanxianqi'])) { ?>(含宽限期<?=$deal['kuanxianqi']?>天)<?php } ?></div>
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
        <input name="money" type="number" id="money" value="" placeholder="请输入投资金额"  class="col-xs-6 safe-lf text-align-lf">
            <div class="col-xs-2 safe-txt">元</div>
        </div>

        <div class="row shouyi">
            <div class="col-xs-4 safe-lf text-align-ct">预计收益</div>
            <div class="col-xs-3 safe-lf text-align-lf yuqishouyi" style="color:#f44336;padding: 0;line-height:35px;font-size:12px;">0.00元</div>
            <div class="col-xs-5" style="padding: 0;text-align: center"><p style="line-height:35px;font-size:12px;">查看<a href="/order/order/agreement?id=<?= $deal->id ?>" style="color:#8c8c8c">《合同协议》</a></p></div>
        </div>
        <div class="row login-sign-btn">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 text-align-ct">
                <input id="buybtn" class="btn-common btn-normal" type="submit" style="background: #F2F2F2;" value="购买">
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>

    <!-- 遮罩层 start  -->
    <div class="mask"></div>
    <!-- 遮罩层 end  -->
    <!-- 输入弹出框 start  -->
    <div class="succeed-info hidden">
        <div class="col-xs-12"><img src="/images/succeed.png" alt="对钩"> </div>
        <div class="col-xs-12">购买成功</div>
    </div>
    <!-- 输入弹出框 end  -->
    <!-- 购买页 end  -->

