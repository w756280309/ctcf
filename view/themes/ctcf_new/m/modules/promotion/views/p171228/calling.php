<?php
use common\models\adv\Share;

$this->title = '好友召集令';
$hostInfo = Yii::$app->params['clientOption']['host']['wap'];
$this->share = new Share([
    'title' => '为我助力，送你50元超市卡！',
    'description' => '偷偷告诉你，这可是专属于咱俩的限时福利哦！',
    'imgUrl' => 'https://static.wenjf.com/upload/link/link1515226721444097.png',
    'url' => $hostInfo.'/promotion/p171228/share?inv='.$userCode,
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171221/css/call-up.css?v=1.121">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<style>
    [v-cloak]{display: none}
</style>
<div class="flex-content" id="app">
    <div id="box-top" :class="{'active':Active}">
        <div class="box-top-mid" style="background: url(<?= FE_BASE_URI ?>wap/campaigns/active20171221/images/shade-contain2-new.png) 0 0 no-repeat;background-size: 100% 100%;height: 10.48rem;">
            <a class="cue-login"></a>
            <div @click="closeBox" class="cue-close">
            </div>
            <p class="active-state">本次活动最终解释权归楚天财富所有</p>
            <div class="mid-box-contain">
                <ol>
                    <li class="clearfix"><span>1、</span><p>活动时间：2018年1月8日至2018年1月20日；</p></li>
                    <li class="clearfix"><span>2、</span><p>活动期间每邀请1位好友通过微信端注册并完成首投（不含新手标及转让），即可获得18.8元奖励金；</p></li>
                    <li class="clearfix"><span>3、</span><p>活动期间奖励金限量1000份，单个用户最多获得6份奖励金；</p></li>
                    <li class="clearfix"><span>4、</span><p>每笔奖励金将立即发放到账户余额，请注意查收；</p></li>
                    <li class="clearfix"><span>5、</span><p>本活动仅限出借用户（不含新手标及转让）参与。</p></li>
                </ol>
            </div>
        </div>
    </div>
    <a @click="showBg" class="a-nav"><span>活动规则</span></a>
    <div class="jackpot-btn">奖池已提取：<span><?= $investMoney ?></span>元</div>
    <p class="msg">每召集1位小伙伴完成首投, </p>
    <p class="msg">奖池将放入<span>18.8元</span>现金红包哦！</p>
    <div class="call-progress" id="call-progress">
        <ul class="ring-bg">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
        <ul class="head-portrait">
            <?php foreach ($urls as $url) { ?>
            <li><img src="<?= $url ?>" alt=""></li>
            <?php } ?>
        </ul>
        <div class="call-progress-tips">小提示：每位用户最多召集6位好友</div>
    </div>
    <div class="get-friend-btn share">召集好友</div>
</div>
<script>
    window.onload = function () {
        FastClick.attach(document.body);
    }
    var app = new Vue({
        el: '#app',
        data: {
            Active: false,
        },
        methods: {

            showBg: function () {
                this.Active = true;
                $('.flex-content').bind('touchmove', function (e) {
                    var e = e || window.event;
                    e.preventDefault();
                })
            },
            closeBox: function () {
                this.Active = false;
                $('.flex-content').unbind('touchmove');
            }
        }
    })
</script>
