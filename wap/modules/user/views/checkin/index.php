<?php

$this->title = '签到得积分';

$hostInfo = \Yii::$app->request->hostInfo;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/qiandao/css/index.css?v=20170401">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="window.location.href='/?_mark=<?= time() ?>'">
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
        <?php } else { ?>
            <a href="<?= $user ? '/mall/portal/guest' : '/site/login?next='.urlencode($hostInfo.'/user/checkin') ?>" class="qiandao_btn btn1">快点我签到吧~</a>
        <?php } ?>
    </div>
    <div class="rule_box" style="padding-top: 0.5rem;">
        <p class="rule_top f15">热烈庆祝“签到”功能上线！</p>
        <p class="rule_top f15">每天签到2积分，4月签到积分直升2.5倍起！</p>
        <p class="rule_top f15">连续签到可得代金券！</p>
        <p class="rule_title f15">签到规则</p>
        <img src="<?= FE_BASE_URI ?>wap/qiandao/images/xingxing.png" alt="">
        <ul class="rule_content f13">
            <li>您每天可以签到1次；</li>
            <li>第1至第7天，每次签到获得5积分（直升2.5倍）；</li>
            <li>连续签到7天，额外送10元代金券；</li>
            <li>第8天起，每次签到，获得8积分（直升4倍）；</li>
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
                <th>起投金额（元）</th>
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
        <?php if (!defined('IN_APP')) { ?>
            <p class="djq_deadline f13"><a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf">使用APP签到更方便，点击<font color="#0080ff">立即下载</font></a></p>
        <?php } ?>
    </div>
</div>

<script>
    $(function () {
        var jindu = '<?= $checkInDays ?>';

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

        forceReload_V2();
    })
</script>
