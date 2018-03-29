<?php

$this->title = '新人首投礼';
?>

<link rel="stylesheet" href="/ctcf/css/active-20180207/base.css">
<script src="/js/lib.flexible3.js"></script>
<link rel="stylesheet" href="/ctcf/css/active-20180207/index.css">
<div class="flex-content" id="app">
    <img src="/ctcf/images/active-20180207/banner.png" alt="" class="wrap-img" />
    <div class="get-detail">
        <p class="title">任务详情</p>
        <p class="txt"><span>888元红包</span><i class="get">注册领取</i></p>
        <p class="txt"><span>50元超市卡</span><i class="get">首次投资领取</i></p>
        <p class="sm-tips">温馨提示：第一笔投资≥5万（此时新手标不参与），奖励可升级成160元超市卡哦！</p>
    </div>
    <a href="<?php if (Yii::$app->user->isGuest) { ?>/site/signup<?php } else { ?>/deal/deal/index<?php } ?>" class="common-btn get-btn">直接领取奖励</a>
    <img src="/ctcf/images/active-20180207/rule-title.png" alt="" class="wrap-img" />
    <ul class="rule">
        <li class="border-pot"></li>
        <li class="border-pot"></li>
        <li class="border-pot"></li>
        <li class="border-pot"></li>
        <li class="title">活动时间：</li>
        <li class="detail time">2018年2月23日起。</li>
        <li class="title">参与活动：</li>
        <li class="detail ">新人注册即可获得<span class="light-txt">888元红包</span>；<br/>新人首次投资任意金额，即可获得<span class="light-txt">1400积分</span>，您可以在积分商城兑换<span class="light-txt">50元超市卡</span>；</li>
        <li class="detail">新人第一笔投资<span class="light-txt">≥5万元</span>（此时新手标不参与），奖励将升级为<span class="light-txt">3500积分</span>，可在积分商城兑换<span class="light-txt">160元超市卡</span>；</li>
        <li class="detail time">超市卡每日限量，以兑换的时间先后顺序进行审核发放。</li>
        <li class="title ">领取奖励：</li>
        <li class="detail">完成活动任务后，红包与积分即时发放到账，您可在账户中心查看；</li>
        <li class="detail time">超市卡将在兑换后的3个工作日内审核发放，如果遇到刷单的情况，公司有权取消活动奖励。如有疑问请联系客服<?= Yii::$app->params['platform_info.contact_tel'] ?>。</li>
        <li class="title">公司地址：</li>
        <li class="detail">武汉市东湖路181号楚天文化创意产业园8号楼1层。</li>
        <li class="last-detail clearfix">
            <div class="lf-txt">
                <p class="weixin">了解更多内容<br/>请添加官方客服微信号：</p>
                <p class="weixin-name">ctcfNO1</p>
            </div>
            <div class="rg-txt">
                <img src="/ctcf/images/active-20180207/qr-code.png" alt="二维码">
            </div>
        </li>
    </ul>
    <a href="/user/invite" class="common-btn invite-btn">邀请好友参与</a>
    <p class="copy-right">未详尽事宜请来电咨询</p>
</div>
