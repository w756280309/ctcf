<?php
$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
$this->title = '签到得积分';

$hostInfo = \Yii::$app->request->hostInfo;
$taps = date('Y-m-d') >= '2017-05-01' && date('Y-m-d') <= '2017-05-31';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css?v=20170906">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/sign-in/index.css?v=2018021211">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt=""
                 <?php if ($backUrl) : ?>
                     onclick="window.location.href='<?= $backUrl ?>'"
                 <?php else : ?>
                     onclick="history.go(-1)"
                 <?php endif; ?>
            >
            签到得积分
        </div>
    <?php } ?>
    <div class="head_part">
        <a href="<?= $user ? '/user/checkin/list' : '/site/login?next='.urlencode($hostInfo.'/user/checkin/list') ?>" class="jilu_link f12">签到记录</a>
    </div>
    <div class="schedule_box">
        <span class="sticker" style="left: 2.7rem"></span>
        <span class="sticker" style="left: 4.8rem"></span>
        <span class="days f9" style="left: 0.53rem;"><span class="f12">1</span>天</span>
        <span class="days jieduan1 f9"><span class="f12">7</span>天</span>
        <span class="days jieduan2 f9"><span class="f12">14</span>天</span>
        <span class="days jieduan3 f9"><span class="f12">30</span>天</span>
        <div class="schedule_bar">
            <span class="jindu_one lf"></span>
            <!--将该span的宽度转换为签到天数*2.8275%-->
            <span class="jindu_two lf"></span>
        </div>
        <img class="yuans jieduan1" src="<?= FE_BASE_URI ?>wap/qiandao/images/yuan_10.png" alt="">
        <img class="yuans jieduan2" src="<?= FE_BASE_URI ?>wap/qiandao/images/yuan_20.png" alt="">
        <img class="yuans jieduan3" src="<?= FE_BASE_URI ?>wap/qiandao/images/yuan_50.png" alt="">
    </div>
    <div class="qiandao_button f16">
        <?php if ($checkInToday) { ?>
            <a href="javascript:void(0)" class="qiandao_btn btn2">已签到</a>
        <?php } elseif ($user) { ?>
            <a href="javascript:void(0)" class="qiandao_btn btn1" id="checkin_btn">快点我签到吧~</a>
        <?php } else { ?>
            <a href="/site/login?next=<?= urlencode($hostInfo.'/user/checkin') ?>" class="qiandao_btn btn1">快点我签到吧~</a>
        <?php } ?>
    </div>
    <div class="rule_box" style="padding-top: 0.5rem;">
        <?= $taps ? '<p class="rule_top f15">热烈庆祝楚天财富成立一周年！</p>' : '' ?>
        <p class="rule_top f15" style="color: #f44336; display: <?= $fromNb ? "none" : "block"?>">关注楚天财富公众号（hbctcf），绑定账户送积分啦！</p>
        <p class="rule_top f15">每天签到2积分，<?= $taps ? '5月签到积分直升3倍起！' : '7天后增至5积分。' ?></p>
        <p class="rule_top f15">连续签到可得代金券！</p>
        <p class="rule_title f15">签到规则</p>
        <img src="<?= FE_BASE_URI ?>wap/qiandao/images/xingxing.png" alt="">
        <ul class="rule_content f13">
            <li>您每天可以签到1次；</li>
            <li>第1至第7天，每次签到获得<?= $taps ? '6积分（直升3倍）' : '2积分' ?>；</li>
            <li>连续签到7天，额外送10元代金券；</li>
            <li>第8天起，每次签到，获得<?= $taps ? '8积分（直升4倍）' : '5积分（直升2.5倍）' ?>；</li>
            <li>连续签到14天，送20元代金券；</li>
            <li>连续签到30天，送50元代金券；</li>
            <li>本活动为周期性连续签到，连续30天签到后，进入下一签到周期；</li>
            <li>签到中断后，则重新从第1天开始计算。</li>
        </ul>
        <p class="rule_title f15" style="padding: 0;">代金券使用规则</p>
        <img src="<?= FE_BASE_URI ?>wap/qiandao/images/xingxing.png" alt="">
        <p class="djq_deadline f13">代金券有效期30天；</p>
        <table class="f13">
            <tr>
                <th>起借金额（元）</th>
                <th>可用代金券</th>
            </tr>
            <tr>
                <td>10,000</td>
                <td><span class="org">10元</span>代金券</td>
            </tr>
            <tr>
                <td>20,000</td>
                <td><span class="org">20元</span>代金券</td>
            </tr>
            <tr>
                <td>50,000</td>
                <td><span class="org">50元</span>代金券</td>
            </tr>
        </table>
    </div>
</div>

<!--添加签到蒙层-->
<div class="mask"></div>
<div class="pomp">
    <img src="<?= FE_BASE_URI ?>wap/qiandao/images/pomp-header.png" alt="">
    <p class="pomp-time"><i></i>您已连续签到<span></span>天<i></i></p>
    <p class="pomp-points">获得<span id="pomp-point"></span>积分<i id="pomp-coupon"></i></p>
    <div class="boundWeixin" style=" display: <?= $fromNb ? "none" : "block"?>">
        <?php if (!$isBindWx) { ?>
        <p>绑定微信额外送10积分啦！</p>
        <?php } ?>
        <p>关注楚天财富微信公众号，<br>点击“绑定账户”即可</p>
    </div>
    <a href="javascript:void(0);">确认</a>
</div>

<script>
    function progressBar(jindu) {
        if (jindu == 0) {
            $('.jindu_one').hide();
            $('.schedule_bar .jindu_two').hide();
        } else if (jindu == 30) {
            $('.schedule_bar .jindu_two').css({'width':90.6+'%','border-radius':0});
        } else {
            $('.schedule_bar .jindu_two').css('width', function () {
                return parseInt(jindu-1) * 3 + '%';
            });
        }
    }

    $(function () {
        progressBar('<?= $checkInDays ?>');

        var allowClick = true;
        $('#checkin_btn').on('click', function() {
            if (!allowClick) {
                 return;
            }

            allowClick = false;

            var xhr = $.get('/user/checkin/check', function(data) {
                $('.pomp-time span').html(data.streak);
                $('#pomp-point').html(data.points);
                if (data.coupon) {
                    $('#pomp-coupon').html('和<span>'+WDJF.numberFormat(data.coupon, true)+'</span>元代金券');
                }
                nr = '<p class="pomp-points">'+(data.points - data.extraPoints - data.promo);
                if (data.extraPoints) {
                    nr += '+<span>'+WDJF.numberFormat(data.extraPoints, true)+'</span>(老用户回归)<br>';
                }
                if (data.promo) {
                    nr += '+<span>'+WDJF.numberFormat(data.promo, true)+'</span>(活动奖励)';
                }
                nr += '</p>';
                if (data.extraPoints || data.promo) {
                    $('.pomp .pomp-points').after(nr);
                }
                WDJF.touchmove(false);
                $('.mask, .pomp').show();
            });

            xhr.fail(function(jqXHR) {
                var msg = jqXHR.status == 400 && jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '系统繁忙，请稍后重试！';

                allowClick = true;
                toastCenter(msg);
            });
        });
        /*添加点击刷新页面*/
        $('.pomp').on('click', function(event) {
            event.stopPropagation();
        });
        $('.pomp a, .mask').on('click', function(event) {
            event.stopPropagation();
            WDJF.touchmove(true);
            window.location.href = '<?= $hostInfo ?>'+'/user/checkin?_mark='+'<?= time() ?>';
        });
        forceReload_V2();
    });
</script>
