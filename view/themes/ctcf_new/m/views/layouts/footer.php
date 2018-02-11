<?php
    $uniqueActionId = Yii::$app->controller->action->uniqueId;
?>
<style>
    /*.navbar-fixed-bottom{bottom:0;margin-bottom:0;border-width:1px 0 0}*/
    /*.navbar-fixed-bottom{position:fixed;right:0;left:0;z-index:1030}*/
    /*.footer{height:50px;background:#fff;box-shadow:0 2px 5px #888}*/
    /*.footer-title{width:33.3333333%;float:left;height:100%;text-align:center}*/
    /*.footer-inner{display:block;height:100%;margin:0 auto;cursor:pointer}*/
    /*.footer-inner1{display:block;height:100%;margin:0 auto;cursor:pointer}*/
    /*.footer-inner2{display:block;height:100%;margin:0 auto;cursor:pointer}*/
    /*.footer-title .shouye{display:block;width:30px;height:30px;margin:0 auto;background:url(/images/footer2.png) no-repeat -145px -3px;background-size:200px;font-size:18px}*/
    /*.special-bar .shouye {background:url(/images/footer2.png) no-repeat -145px -57px;background-size:200px;}*/
    /*.footer-title .licai{display:block;width:30px;height:30px;margin:0 auto;background:url(/images/footer2.png) no-repeat -113px -3px;background-size:200px;font-size:18px}*/
    /*.special-bar .licai {background:url(/images/footer2.png) no-repeat -113px -57px;background-size:200px;}*/
    /*.footer-title .zhanghu{display:block;width:30px;height:30px;margin:0 auto;background:url(/images/footer2.png) no-repeat -81px -3px;background-size:200px;font-size:18px}*/
    /*.special-bar .zhanghu {background:url(/images/footer2.png) no-repeat -81px -57px;background-size:200px;}*/
    /*.footer-title span img{width:26px}*/
    /*.nav-bar{font-size:14px;line-height:1.5}*/
    /*.special-bar {  color: rgb(244, 67, 54);  }*/
    .navbar-fixed-bottom {
        bottom: 0;
        margin-bottom: 0;
        border-width: 1px 0 0
    }

    .navbar-fixed-bottom {
        position: fixed;
        right: 0;
        left: 0;
        z-index: 1030
    }

    .footer {
        height: 50px;
        background: #fff;
        box-shadow: 0 2px 5px #888
    }

    .footer-title {
        width: 33.3333333%;
        float: left;
        height: 100%;
        text-align: center
    }

    .footer-inner {
        display: block;
        height: 100%;
        margin: 0 auto;
        cursor: pointer
    }

    .footer-inner1 {
        display: block;
        height: 100%;
        margin: 0 auto;
        cursor: pointer
    }

    .footer-inner2 {
        display: block;
        height: 100%;
        margin: 0 auto;
        cursor: pointer
    }

    .footer-title .shouye {
        display: block;
        width: 30px;
        height: 30px;
        margin: 0 auto;
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_home.png) no-repeat center center;
        background-size: 80% 80%;
        font-size: 18px
    }

    .special-bar .shouye {
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_home2.png) no-repeat center center;
        background-size: 80% 80%;
    }

    .footer-title .licai {
        display: block;
        width: 30px;
        height: 30px;
        margin: 0 auto;
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_list.png) no-repeat center center;
        background-size: 80% 80%;
        font-size: 18px
    }

    .special-bar .licai {
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_list2.png) no-repeat center center;
        background-size: 80% 80%;
    }

    .footer-title .zhanghu {
        display: block;
        width: 30px;
        height: 30px;
        margin: 0 auto;
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_account.png) no-repeat center center;
        background-size: 80% 80%;
        font-size: 18px
    }

    .special-bar .zhanghu {
        background: url(<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/icon_footer_account2.png) no-repeat center center;
        background-size: 80% 80%;
    }

    .footer-title span img {
        width: 26px
    }

    .nav-bar {
        font-size: 14px;
        line-height: 1.5
    }

    .special-bar {
        color: rgb(244, 67, 54);
    }

</style>
<div class="navbar-fixed-bottom footer">
    <div class="footer-title">
        <div class="footer-inner">
            <?php if($uniqueActionId === 'site/index') {?>
                <a href="/#t=1" class="nav-bar special-bar"><span class="shouye"></span>首页</a>
            <?php } else {?>
                <a href="/#t=1" class="nav-bar"><span class="shouye"></span>首页</a>
            <?php }?>
        </div>
    </div>
    <div class="footer-title">
        <div class="footer-inner1">
            <?php if($uniqueActionId === 'deal/deal/index') {?>
                <a href="/deal/deal/index" class="nav-bar special-bar"><span class="licai"></span>投资</a>
            <?php } else {?>
                <a href="/deal/deal/index" class="nav-bar"><span class="licai"></span>投资</a>
            <?php }?>
        </div>
    </div>
    <div class="footer-title">
        <div class="footer-inner2">
            <?php if($uniqueActionId === 'user/user/index') {?>
                <a class="nav-bar special-bar" href="/user/user" style="color: rgb(244, 67, 54)"><span class="zhanghu"></span>账户</a>
            <?php } else {?>
                <a class="nav-bar" href="/user/user"><span class="zhanghu"></span>账户</a>
            <?php }?>
        </div>
    </div>
</div>
