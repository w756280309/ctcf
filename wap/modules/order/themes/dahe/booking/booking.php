<?php
$this->title = '预约申请单';
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css?v=20160401"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/chongzhi.css?v=20160401"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/tixian.css?v=20160401"/>


<!--提现金额-->
<form method="post" class="cmxform" id="form" action="/order/booking/booking?pid=<?= $product->id ?>" data-to="1">
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="row kahao">
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-7 col-sm-8"><input id="" type="text" name="BookingLog[name]" placeholder="请输入您的姓名"/></div>
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-1 col-sm-1"></div>
        <div class="hidden-xs col-sm-1"></div>
    </div>
    <div class="row kahao">
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-7 col-sm-8"><input id="" type="text" name="BookingLog[mobile]" placeholder="请输入您的手机号"/></div>
        <div class="col-xs-3 col-sm-1"></div>
        <div class="col-xs-1 col-sm-1"></div>
        <div class="hidden-xs col-sm-1"></div>
    </div>
    <div class="row kahao">
        <div class="col-xs-8 col-sm-8"><input id="" type="text" name="BookingLog[fund]" placeholder="请输入预约的金额"></div>
        <div class="col-xs-4 col-sm-4" style="text-align: right;">万</div>
    </div>
    <div class="row">
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-7 col-sm-8"><p class="p-notice">*股权项目起购金额<?= $product->min_fund ?>万元</p></div>
        <div class="hidden-xs col-sm-1"></div>
    </div>
    <!--提交按钮-->
    <div class="row ">
        <div class="col-xs-3"></div>
        <div class="col-xs-6 login-sign-btn marg-top-btn">
            <input id="booking_btn" class="btn-common btn-normal" type="button" value="提交">
        </div>
        <div class="col-xs-3"></div>
    </div>
</form>

<script type="text/javascript">
    var csrf;
    $(function() {
       csrf = $("meta[name=csrf-token]").attr('content');
       $('#booking_btn').bind('click',function() {
           $(this).addClass("btn-press").removeClass("btn-normal");
           subForm("#form", "#booking_btn");
           $(this).removeClass("btn-press").addClass("btn-normal");
       });
    })
 </script>
