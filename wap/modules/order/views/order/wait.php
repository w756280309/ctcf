<?php
$this->title= "订单处理中";

$this->registerJsFile(ASSETS_BASE_URI.'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160331">
<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <div>订单处理中……</div>
    </div>
</div>
<div class="row" id='bind-true'>

</div>
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12 page_padding">
        <div>遇到问题请联系客服，电话：<?= Yii::$app->params['contact_tel'] ?></div>
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
function ret()
{
    $.ajax({url: "/order/order/ordererror?osn=<?= $order->sn?>", success: function(data){
        if (0 !== data.status) {
            location.replace("/order/order/ordererror?osn=<?= $order->sn?>");
        }
      }});
}
$(function () {
    var int = setInterval(ret, 1000);
    setTimeout(function () {
        clearInterval(int);
        //location.replace("/order/order/ordererror?osn=<?= $order->sn?>");
    }, 5000);//3秒之后自动跳入结果页面
})
</script>