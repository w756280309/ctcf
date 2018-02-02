<?php

$this->title = '双12狂欢';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171212/css/index.css?v=1.6">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<!--[if lt IE 9]>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20171212/js/html5shiv.js"></script>
<![endif]-->
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script>
    var feBaseUrl = '<?= FE_BASE_URI ?>';
    var accountBalance = '<?= $availableBalance ?>';
</script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20171212/js/index.js?v=1.2"></script>
<div class="box-top">
    <div class="box-top-mid">
        <div class="cue-close">
        </div>
        <div class="box-contain">
            <p class="p-hint">您已获得<span>0</span>张加息券，继续充值 还能获得更高价值加息券哦！</p>
            <div class="a-div clearfix">
                <a href="/deal/deal/index?t=1">使用加息券</a>
                <a href="/user/userbank/recharge">继续充值</a>
            </div>
            <i class="mid-wire"></i>
            <p class="p-mid">已获得的加息券：</p>
            <div class="contain-quan" id="wrapper">
                <ul>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="contain">
    <header></header>
    <main class="main">
        <ul>
            <li>
                <span>50000</span>
                <span>0.5%7天加息券</span>
            </li>
            <li>
                <span>100000</span>
                <span>1.0%7天加息券</span>
            </li>
            <li>
                <span>200000</span>
                <span>1.5%7天加息券</span>
            </li>
            <li>
                <span>500000</span>
                <span>2.5%7天加息券</span>
            </li>
            <li>
                <span>1000000</span>
                <span>3.0%7天加息券</span>
            </li>

        </ul>
        <div class="notice">
            注：因手机端快捷充值限额，大额充值请使用电脑登录温都金服官网(www.wenjf.com)进行网银充值。
        </div>
        <p>
            <span>当前账户余额:</span>
            <i class="money-amounts">*****</i>
            <span>万元</span>
            <img class="ice-thing" src="<?= FE_BASE_URI ?>wap/campaigns/active20171212/images/bg-ice2.png">
        </p>
        <nav class="clearfix">
            <div>
                <a href="/user/userbank/recharge">继续充值</a>
            </div>
            <div>
                <a class="get-tecket">领走加息券</a>
            </div>
        </nav>
    </main>
    <footer>
        <div class="footer-top-contain">
            <h5>活动规则：</h5>
            <p>1. 活动时间12月12日-18日；</p>
            <p>2. 活动期间账户余额达到相应金额，可获得对应起投金额加息券1张，活动期间最多领取5张；</p>
            <p>3. 加息券仅限活动期间使用！</p>
        </div>

    </footer>
    <div class="footer-bottom-contain">
        <p>本活动最终解释权归温都金服所有</p>
    </div>
</div>
<script type="text/template" id="addTicket">
    {{#each this}}
    <li>
        <span class="span-left">
            <img src='<?= FE_BASE_URI ?>{{path}}' alt="">
        </span>
        <span class="span-right">{{name}}</span>
    </li>
    {{/each}}
</script>