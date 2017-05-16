<?php

use common\utils\StringUtils;

$this->title = '0元夺宝送iPhone7';
$this->share = $share;
$this->headerNavOn = true;

$isLogin = !\Yii::$app->user->isGuest;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170504/css/index.css?v=20170516">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/banner.png" alt="图">
    </div>
    <div class="gift">
        <h5>奖品：iPhone7 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数量：1部</h5>
        <img class="guoqi" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/guoqi.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/phone.png" alt="">
        <div class="progress">
            <p class="progressTotal"><span class="progressLine" style="width: <?= $jindu ?>%;"></span></p>
            <p class="progressRate">揭晓进度<span><?= $jindu ?>%</span></p>
            <div class="totalNum clearfix"><span class="lf">总需2000人</span><span class="rg">剩余<i><?= 2000 - $totalTicketCount ?></i></span></div>

            <?php if ($isLogin && $isJoinWith && $joinTicket) { ?>
                <button class="join joined">夺宝码：<?= $joinTicket->getCode() ?></button>
            <?php } else { ?>
                <button class="join-btn joined">参与夺宝</button>
            <?php } ?>
        </div>
    </div>
    <div class="rule">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/rule.png" alt="">
        <ul class="rules">
            <li>1、本次活动仅限浙江手机号段用户参与；</li>
            <li>2、活动期间新注册用户可以获得参与机会，老用户成功邀请好友后也可以获得参与机会；</li>
            <li>3、本次活动限额2000名，满额后将不能参与，活动结束前未满额将取消开奖；</li>
            <li>4、本次活动所有未中奖用户将于开奖当日获赠神秘礼包1份；</li>
            <li>5、领取活动奖品需要实名认证并绑定银行卡；</li>
            <li>6、活动时间2017年5月6日-5月15日，开奖时间为2017年5月17日中午12点。</li>
        </ul>
        <ul class="regular">
            <li>计算规则如下：</li>
            <li>1、参与用户随机获得1个夺宝码（1000001-1002000）；</li>
            <li>2、选择最近10位参与用户的参与时间之和，加上第2017056期双色球号码，然后进行求余：【最近10位参与用户的参与时间之和+双色球号码】% 2000（参与人数），得到余数；</li>
            <li>3、中奖号码=余数+1000001。</li>
        </ul>
    </div>

    <?php if (!empty($promoLotteryQuery)) { ?>
        <div class="userList">
            <ul class="listBox">
                <?php
                $mobile = [
                    '0' => '138******',
                    '1' => '136******',
                    '2' => '130******'
                ];
                foreach ($promoLotteryQuery as $query) { ?>
                <li class="clearfix">

                    <?php

                        if ($query->source == "fake") {
                            $crc32_id = crc32($query->id);
                    ?>
                        <span class="lf"><?= $mobile[substr($crc32_id, 0, 4)%3] . substr($crc32_id, 0, 2)?></span>
                    <?php
                        } else {
                    ?>
                        <span class="lf"><?= StringUtils::obfsMobileNumber($query->user->getMobile()) ?></span>
                    <?php  } ?>

                        <span class="rg"><?= date('n-d', $query->created_at) ?> &nbsp;&nbsp;&nbsp;&nbsp;<?= date('H:i:s', $query->created_at) ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <p class="text-align-ct tips">本次活动最终解释权归温都金服所有<br>
        理财非存款 产品有风险 投资须谨慎
    </p>
</div>