<?php

$this->title= '订单处理中';

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/buy.css');

?>

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content">
                <p class="buy-txt"><span>订单处理中……</span></p>
                <p class="buy-txt-tip">遇到问题请联系客服，电话：<?= Yii::$app->params['platform_info.contact_tel'] ?> <a href="javascript:void(0)" onclick="location.replace('/user/user/myorder?type=2')" class="bind-close1">查看订单</a></p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var orderSn = '<?= $order->sn ?>';
    if (typeof ga != 'undefined') {
        ga('require', 'ecommerce');

        function logTx()
        {
            if (Cookies.get('fin_tid') === orderSn) {
                return;
            }

            if ('undefined' !== typeof _paq) {
                _paq.push([
                    'trackEcommerceOrder',
                    orderSn,
                    '<?= $order->order_money ?>'
                ]);
            }

            Cookies.set('fin_tid', orderSn);

            ga('ecommerce:addTransaction', {
                'id': orderSn,
                'revenue': '<?= $order->order_money ?>',
                'hitCallback': function() {
                    location.replace('/order/order/result?status=success&osn='+orderSn);
                }
            });

            ga('ecommerce:send');
        }
    }


    function ret()
    {
        var toUrl = '/order/order/result?osn='+orderSn;

        $.ajax({
            url: toUrl,
            success: function(data) {
                if (0 !== data.status) {
                    if (1 === data.status) {
                        toUrl = toUrl+'&status=success'
                    } else if (2 === data.status) {
                        toUrl = toUrl+'&status=fail'
                    }

                    if (typeof ga != 'undefined') {
                        if (1 === data.status) {
                            logTx();
                        }

                        setTimeout(function() {
                            location.replace(toUrl);
                        }, 1500);
                    } else {
                        location.replace(toUrl);
                    }
                }
            }
        });
    }

    var int = setInterval(ret, 1000);
    setTimeout(function () {
        clearInterval(int);
        location.replace("/user/user/myorder?type=2");
    }, 5000);//3秒之后自动跳入结果页面
</script>
