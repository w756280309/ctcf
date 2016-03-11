<?php
$this->title="系统设置";
?>
<link rel="stylesheet" href="/css/base.css">
<link rel="stylesheet" href="/css/setting.css">

    <div class="row">
    <div class="row bg-height border-bottom">
        <div class="col-xs-3 left-txt">用户头像</div>
        <div class="col-xs-7 ">
            ID:<?= $model->usercode ?>
        </div>
        <div class="col-xs-2 headpic">
            <img class="headpic-img-box" src="/images/headpic.png" width="40px" hieght="40px" alt="头像">
        </div>
    </div>
    <div class="row sm-height border-bottom">
        <div class="col-xs-3 left-txt">绑定手机</div>
        <div class="col-xs-3"></div>
        <div class="col-xs-5 left-txt"><?= substr_replace($model->mobile,'****',3,-4) ?></div>
        <div class="hidden-xs col-sm-1 "></div>
    </div>
    <div class="row sm-height border-bottom margin-top" onclick="window.location.href='/system/system/safecenter'">
        <div class="col-xs-3 col-sm-3 left-txt">安全中心</div>
        <div class="col-xs-7 col-sm-7"></div>
        <div class="col-xs-1 col-sm-1 arrow">
            <a href="/system/system/safecenter"><img src="/images/arrow.png" alt="右箭头"></a>
        </div>
        <div class="col-xs-1 col-sm-1 "></div>
    </div>
    <div class="clear"></div>
    <div class="row sm-height border-bottom" onclick="window.location.href='/system/system/help'">
        <div class="col-xs-3 left-txt">帮助中心</div>
        <div class="col-xs-7"></div>
        <div class="col-xs-1 arrow">
            <a href="/system/system/help"><img src="/images/arrow.png" alt="右箭头"></a>
        </div>
        <div class="col-xs-1 col-sm-1 "></div>
    </div>
    <div class="clear"></div>
    <div class="row sm-height border-bottom" onclick="window.location.href='/system/system/problem'">
        <div class="col-xs-3 left-txt">常见问题</div>
        <div class="col-xs-7"></div>
        <div class="col-xs-1 arrow">
            <a href="/system/system/problem"><img src="/images/arrow.png" alt="右箭头"></a>
        </div>
        <div class="hidden-xs col-sm-1 "></div>
    </div>
    <div class="clear"></div>
    <div class="row sm-height border-bottom" onclick="window.location.href='/system/system/about'">
        <div class="col-xs-3 left-txt">关于我们</div>
        <div class="col-xs-7"></div>
        <div class="col-xs-1 arrow">
            <a href="/system/system/about"><img src="/images/arrow.png" alt="右箭头"></a>
        </div>
        <div class="col-xs-1"></div>
    </div>
    <div class="clear"></div>
</div>
    <!-- 系统设置 end  -->
    <!-- 输入弹出框 start  -->
    <div class="error-info">您输入的密码不正确</div>
    <!-- 输入弹出框 end  -->
