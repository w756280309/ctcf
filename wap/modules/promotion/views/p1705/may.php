<?php

$this->title = '温都金服一周年瓜分百万礼品';
$this->share = $share;
$this->headerNavOn = true;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170424/css/index.css?v=1.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/anniversary/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content">
    <div class="part-one">
        <?php
            if (Yii::$app->user->isGuest) {
        ?>
                <span class="xunzhang">我的勋章：未登录</span>
        <?php
            } else {
        ?>
                <span class="xunzhang">我的勋章：<span class="num"><?= $xunzhang ?></span> 枚</span>
         <?php
            }
         ?>
    </div>
    <div class="part-two"></div>
    <div class="part-three">
        <div class="rules-box">
            <p class="rules-title">活动规则</p>
            <ul class="rules-content">
                <li>活动时间2017年4月29日-5月20日；</li>
                <li>活动期间投资任意金额（不含新手标及转让产品），即可获得周年庆勋章1枚；</li>
                <li>活动期间每邀请1位好友注册绑卡，可获得1枚勋章（最多3枚）；</li>
                <li>周年庆勋章可于5月20日当天兑换现金红包，最高520元。</li>
            </ul>
        </div>
    </div>
    <div class="part-four">
        <a href="/user/checkin" class="button-qiandao"></a>
    </div>
    <div class="part-bottom">
        <div class="part-bottom-one">
            <a class="wuyi-link">
                <img class="wuyi gray" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-wuyi.png" alt="" style="top: 1.44rem;right:0.533rem ">
            </a>
            <a class="wusi-link">
                <img class="wusi gray" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-wusi.png" alt="" style="bottom:0.9rem;left: 0.533rem;">
            </a>
        </div>
        <div class="part-bottom-two">
            <a class="muqin-link">
                <img class="muqin gray" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-muqin.png" alt="" style="top: 0.2133rem;right:0.64rem;">
            </a>
            <a class="zhounian-link">
                <img class="zhounian gray" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-zhounian.png" alt="" style="left:1.333rem;top: 4.133rem;">
            </a>
            <a class="coins-link">
                <img class="coins gray" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic_coins.png" alt="" style="left:2.75rem;bottom: 0;">
            </a>
        </div>
        <div class="part-bottom-buttons">
            <a href="/user/invite" class="invest" style="left:0.426rem;bottom: 0.333rem;"></a>
            <a href="/deal/deal/index" class="invite" style="right:1.2rem;bottom: 0.5rem;"></a>
        </div>
    </div>
</div>
<script>
    var nowDate = (new Date()).valueOf();
    //五一节活动时间节点
    var wuyiBegin = (new Date("2017-04-29 00:00:00")).valueOf();
    var wuyiOver = (new Date("2017-05-01 23:59:59")).valueOf();
    //青年节活动时间节点
    var wusiBegin = (new Date("2017-05-04 00:00:00")).valueOf();
    var wusiOver = (new Date("2017-05-07 23:59:59")).valueOf();
    //母亲节活动时间节点
    var muqinBegin = (new Date("2017-05-10 00:00:00")).valueOf();
    var muqinOver = (new Date("2017-05-14 23:59:59")).valueOf();
    //周年庆活动时间节点
    var zhounianBegin = (new Date("2017-05-15 00:00:00")).valueOf();
    var zhounianOver = (new Date("2017-05-19 23:59:59")).valueOf();
    //520
    var wu20Begin = (new Date("2017-05-20 00:00:00")).valueOf();
    var wu20Over = (new Date("2017-05-20 23:59:59")).valueOf();
    //五一
    if(nowDate >= wuyiBegin && nowDate < wuyiOver){
        $('.wuyi').removeClass('gray');
        $('.wuyi-link').attr('href','/promotion/p1705/may-day')
    }
    //五四
    if(nowDate >= wusiBegin && nowDate < wusiOver){
        $('.wusi').removeClass('gray');
        $('.wusi-link').attr('href','/promotion/p1705/youth-day')
    }
    //母亲
    if(nowDate >= muqinBegin && nowDate < muqinOver){
        $('.muqin').removeClass('gray');
        $('.muqin-link').attr('href','/promotion/p1705/mother-day')
    }
    //周年庆
    if(nowDate >= zhounianBegin && nowDate < zhounianOver){
        $('.zhounian').removeClass('gray');
        $('.zhounian-link').attr('href','/promotion/p1705/year-day')
    }
    //520
    if(nowDate >= wu20Begin && nowDate < wu20Over){
        $('.coins').removeClass('gray');
        $('.coins-link').attr('href','/promotion/p1705/520-day')
    }
</script>
