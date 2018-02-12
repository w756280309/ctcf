<?php
$this->title= "订单处理中";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/buy-setting/setting.css?v=201802111">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <div>订单处理中……</div>
    </div>
</div>
<div class="row" id='bind-true'>

</div>
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12 page_padding">
        <div>遇到问题请联系客服，电话：<a style="color: #aab2bd;" class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
    </div>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="javascript:void(0)" onclick="location.replace('/user/user/myorder')" class="bind-close1">查看订单</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<script type="text/javascript">
    var orderId = '<?= $order_id ?>';

    function ret()
    {
        $.ajax({
            url: "/credit/order/refer?id=" + orderId,
            success: function(data) {
                if (0 === data.status || 1 === data.status) {
                    location.replace("/credit/order/refer?id=" + orderId);
                }
            }
        });
    }

    var tick = setInterval(ret, 1000);
    setTimeout(function () {
        clearInterval(tick);
    }, 5000);
</script>
