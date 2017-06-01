<?php

$this->title = '新用户专享活动';
$this->share = $share;
$this->headerNavOn = true;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170527/css/share.css?v=1.0">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content">
    <div class="shareBanner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/share_banner.png" alt="">
        <div class="skewTitle">您的好友138****3874正在温都金服抽奖,<br>邀请您接力</div>
    </div>
    <div class="content">
        <div class="showTitle">奖品展示</div>
        <div class="gifts clearfix">
            <img class="lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/shareGift_01.png" alt="">
            <img class="lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/shareGift_02.png" alt="">
            <img class="rg" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/shareGift_03.png" alt="">
        </div>
        <div class="relay clearfix">
            <img class="relayDo lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/share_do.png" alt="">
            <a class="rg" href="">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/share_weDo.png" alt="">
            </a>
        </div>
        <div class="progress clearfix">
            <div class="lf">接力进度:</div>
            <ul class="rg clearfix">
                <li class="lf">1</li>
                <li class="lf"></li>
                <li class="lf">2</li>
                <li class="lf"></li>
                <li class="lf"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/complete.png" alt=""></li>
            </ul>
        </div>

        <div class="intro">
            <p class="introTitle">
                关于温都金服
            </p>
            <p class="introDetail">温州报业旗下专业理财平台,国资背景、 收益稳健，首投还送50元超市卡。</p>
            <div class="clearfix">
                <dl class="lf">
                    <dt><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/intro_01.png" alt=""></dt>
                    <dd>注册即送<br>288元大红包</dd>
                </dl>
                <dl class="lf">
                    <dt><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170527/images/intro_02.png" alt=""></dt>
                    <dd>首次投资<br>送50元超市卡</dd>
                </dl>
            </div>

            <a class="goRegister" href="/site/signup?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>">注册</a>
        </div>

        <div class="intro platform">
            <p class="introTitle">平台优势</p>
            <ul class="clearfix">
                <li class="lf platformList">
                    <div class="platformListFirst">
                        <img alt="" src="https://static.wenjf.com/v2/wap/index/images/platform_01.png" style="display: inline;">
                        <div class="f15">国资背景</div>
                        <div class="f14">温州报业传媒旗下</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListSecond">
                        <img style="width: 1.1rem; display: inline;" alt="" src="https://static.wenjf.com/v2/wap/index/images/platform_02.png">
                        <div class="f15">资金稳妥</div>
                        <div class="f14">第三方资金托管</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div>
                        <img alt="" src="https://static.wenjf.com/v2/wap/index/images/platform_03.png" style="display: inline;">
                        <div class="f15">收益稳健</div>
                        <div class="f14">预期收益5.5~9%</div>
                    </div>
                </li>
                <li class="lf platformList">
                    <div class="platformListLast">
                        <img alt="" src="https://static.wenjf.com/v2/wap/index/images/platform_04.png" style="display: inline;">
                        <div class="f15">产品优质</div>
                        <div class="f14">国企、政信类产品</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(".relayDo").on("click",function(){
            $(".progress ul li").eq(0).addClass("process");
            $(".progress ul li").eq(1).addClass("process");
        })
    })
</script>