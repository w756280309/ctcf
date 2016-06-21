<?php
$this->title= "订单处理中";
\frontend\assets\FrontAsset::register($this);
$this->registerCssFile('/css/deal/buy.css');
?>
<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content">
                <p class="buy-txt"><span>订单处理中……</span></p>
                <p class="buy-txt-tip">遇到问题请联系客服，电话：<?= Yii::$app->params['contact_tel'] ?> <a href="javascript:void(0)" onclick="location.replace('/user/user/myorder')" class="bind-close1">查看订单</a></p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function ret()
{
    $.ajax({url: "/order/order/ordererror?osn=<?= $order->sn?>", success: function(data){
        if (0 !== data.status) {
            location.replace("/info/success?source=touzi&jumpUrl=/licai/index");
        }
      }});
}
$(function () {
    var int = setInterval(ret, 1000);
    setTimeout(function () {
        clearInterval(int);
    }, 5000);//3秒之后自动跳入结果页面
})
</script>