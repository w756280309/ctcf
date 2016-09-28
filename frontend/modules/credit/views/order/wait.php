<?php
$this->title= "订单处理中";

$this->registerCssFile(ASSETS_BASE_URI . 'css/deal/buy.css');
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

    function request() {
        $.post('/credit/order/wait', {
            "_csrf": "<?= Yii::$app->request->csrfToken?>",
            "order_id":<?= $order_id?>
        }, function (data) {
            if (data.code === 0) {
                window.location.href = data.url;
            }
        })
    }

    var int = setInterval(request, 1000);

    setTimeout(function () {
        clearInterval(int);
        location.replace("/user/user/myorder");
    }, 10000);//5秒之后自动跳入我的订单
</script>
