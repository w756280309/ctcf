<?php

$this->title = '温都金服一周年瓜分百万礼品';
$this->share = $share;
$this->headerNavOn = true;

$act1 = date('Y-m-d') >= '2017-04-29' && date('Y-m-d') <= '2017-05-01';
$act2 = date('Y-m-d') >= '2017-05-04' && date('Y-m-d') <= '2017-05-07';
$act3 = date('Y-m-d') >= '2017-05-10' && date('Y-m-d') <= '2017-05-14';
$act4 = date('Y-m-d') >= '2017-05-15' && date('Y-m-d') <= '2017-05-19';
$act5 = date('Y-m-d') >= '2017-05-20' && date('Y-m-d') <= '2017-05-31';
//到达活动日期后，添加url，并保留
$url1 = date('Y-m-d') >= '2017-04-29' ? '/promotion/p1705/may-day' : null;
$url2 = date('Y-m-d') >= '2017-05-04' ? '/promotion/p1705/youth-day' : null;
$url3 = date('Y-m-d') >= '2017-05-10' ? '/promotion/p1705/mother-day' : null;
$url4 = date('Y-m-d') >= '2017-05-15' ? '/mall/portal/guest?dbredirect=https%3A%2F%2Factivity.m.duiba.com.cn%2Fquestion%2Findex%3Fid%3D2155353%26dblanding%3Dhttps%253A%252F%252Factivity.m.duiba.com.cn%252FactivityShare%252FgetActivityShareInfo%253FoperatingActivityId%253D2155353' : null;
$url5 = date('Y-m-d') >= '2017-05-20' ? '/promotion/p1705/520-day' : null;

?>
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
                <span class="xunzhang">我的勋章：<a href="/site/login" style="color: #fff">未登录</a></span>
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
            <a class="wuyi-link" <?= $url1 ? "href=$url1" : ''?>>
                <img class="wuyi <?= $act1 ? '' : 'gray'?>" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-wuyi-1.png" alt="" style="top: 1.44rem;right:0.533rem ">
            </a>
            <a class="wusi-link" <?= $url2 ? "href=$url2" : ''?>>
                <img class="wusi <?= $act2 ? '' : 'gray'?>" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-wusi.png" alt="" style="bottom:0.9rem;left: 0.533rem;">
            </a>
        </div>
        <div class="part-bottom-two">
            <a class="muqin-link" <?= $url3 ? "href=$url3" : ''?>>
                <img class="muqin <?= $act3 ? '' : 'gray'?>" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-muqin.png" alt="" style="top: 0.2133rem;right:0.64rem;">
            </a>
            <a class="zhounian-link" <?= $url4 ? "href=$url4" : ''?>>
                <img class="zhounian <?= $act4 ? '' : 'gray'?>" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic-zhounian.png" alt="" style="left:1.333rem;top: 4.133rem;">
            </a>
            <a class="coins-link" <?= $url5 ? "href=$url5" : ''?>>
                <img class="coins <?= $act5 ? '' : 'gray'?>" src="<?= FE_BASE_URI ?>wap/campaigns/anniversary/images/pic_coins.png" alt="" style="left:2.75rem;bottom: 0;">
            </a>
        </div>
        <div class="part-bottom-buttons">
            <a href="/user/invite" class="invest" id="invest" style="left:0.426rem;bottom: 0.333rem;"></a>
            <a href="/deal/deal/index" class="invite" style="right:1.2rem;bottom: 0.5rem;"></a>
        </div>
    </div>
</div>


