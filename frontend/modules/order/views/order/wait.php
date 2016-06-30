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
    ga('require', 'ecommerce');

    var orderSn = '<?= $order->sn ?>';
    function logTx()
    {
        if ($.cookie('fin_tid') === orderSn) {
            return;
        }

        ga('ecommerce:addTransaction', {
            'id': orderSn,
            'revenue': '<?= $order->order_money ?>',
            'hitCallback': function() {
                $.cookie('fin_tid', orderSn);
                location.replace("/order/order/ordererror?osn="+orderSn);
            }
        });

        ga('ecommerce:send');
    }

    function ret()
    {
        $.ajax({
            url: "/order/order/ordererror?osn=<?= $order->sn ?>",
            success: function(data) {
                if (0 !== data.status) {
                    if (1 === data.status) {
                        logTx();
                    }

                    setTimeout(function() {
                        location.replace("/order/order/ordererror?osn="+orderSn);
                    }, 1500);
                }
            }
        });
    }

    var int = setInterval(ret, 1000);

    setTimeout(function () {
        clearInterval(int);
        location.replace("/order/order/ordererror?osn="+orderSn);
    }, 5000);//3秒之后自动跳入结果页面
</script>