<?php

$this->title = '女神节理财送大礼';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/women-festvial/css/index.css?v=6">
<script src="<?= FE_BASE_URI ?>/libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>

<?php if (!defined('IN_APP')) { ?>
    <div class="header">
        <ul class="clearfix">
            <li class="lf f16"><img src="<?= FE_BASE_URI ?>wap/wendumao/images/logo.png" alt="">楚天财富国资平台</li>
            <li class="rg f13"><a class="" href="/">返回首页</a></li>
        </ul>
    </div>
<?php } ?>

<div class="part-one"></div>
<div class="part-two"></div>
<div class="part-three">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/women-festvial/images/title.png" alt="">
    <ol class="rules f13">
        <li class="rule">此活动为限时活动，仅为3月7日、8日两天，以出借成功时间为准；</li>
        <li class="rule">本次活动面向所有楚天财富注册用户；</li>
        <li class="rule">活动期间新出借楚天财富平台产品累计金额达到指定额度，即可获得相应礼品(不含转让产品)；</li>
    </ol>
    <p class="f12">各款女神产品累计金额及对应礼物如下：</p>
</div>
<div class="part-four"></div>
<div class="part-five">
    <ol class="rules f13" type="1">
        <li class="rule" value="4">活动结束后3个工作日内，工作人员会与您联系确认领取奖品事宜，请保持通讯畅通。</li>
    </ol>
    <a class="btn f16" href="/deal/deal/index">去理财</a>
</div>
<script>
    window.onload = function () {
        FastClick.attach(document.body);
    }
</script>