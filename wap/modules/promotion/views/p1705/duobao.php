<?php

use common\utils\SecurityUtils;
use common\utils\StringUtils;

$this->title = '0元夺宝送iPhone7';
$this->share = $share;
$this->headerNavOn = true;

$isLogin = !\Yii::$app->user->isGuest;
$next = Yii::$app->request->hostInfo.'/promotion/p1705/duobao';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170504/css/index.css?v=1.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/com.js"></script>

<div class="flex-content">
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/banner.png" alt="图">
    </div>
    <div class="gift">
        <h5>奖品：iPhone7 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数量：1部</h5>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/phone.png" alt="">
        <div class="progress">
            <p class="progressTotal"><span class="progressLine" style="width: <?= $jindu ?>%;"></span></p>
            <p class="progressRate">揭晓进度<span><?= $jindu ?>%</span></p>
            <div class="totalNum clearfix"><span class="lf">总需2000人</span><span class="rg">剩余<i><?= 2000 - $totalTicketCount ?></i></span></div>

            <?php if ($isLogin && $isJoinWith && $joinTicket) { ?>
                <button class="join joined">夺宝码：<?= $joinTicket->getCode() ?></button>
            <?php } else { ?>
                <button class="join-btn">参与夺宝</button>
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
    <div class="mask"></div>
    <div class="pomp note">
        <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/close.png" alt="">
        <p>活动未开始</p>
    </div>
    <div class="pomp noChange">
        <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/close.png" alt="">
        <img style="width: 3.653333rem;" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/no-change.png" alt="">
        <p>您的手机号段没有夺宝资格去看看其他活动吧！</p>
    </div>
    <div class="pomp login">
        <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/close.png" alt="">
        <img style="width: 1.3466667rem;" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/login.png" alt="">
        <p>您还没有登录哦<br>快去登录获得夺宝机会吧！</p>
        <a href="/site/login?next=<?= urlencode($next) ?>">去登录</a>
    </div>
    <div class="pomp address">
        <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/close.png" alt="">
        <img style="width: 1.3466667rem;" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/address.png" alt="">
        <p>完善个人信息<br>就有机会拿走大奖了哦！</p>
        <a href="/user/bank">去完善</a>
    </div>
    <div class="pomp invite">
        <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/close.png" alt="">
        <img style="width: 4.2533333rem;" src="<?= FE_BASE_URI ?>wap/campaigns/active20170504/img/invite.png" alt="">
        <p>您还没有参与机会<br>快去邀请好友吧！</p>
        <a href="/user/invite">去邀请</a>
    </div>
    <div class="bing-info pop">
        <div class="bing-tishi">请输入领取的手机号码</div>
        <p class="tishi-p" style="margin: 20px auto !important;">
            <input class="f15" type="tel" maxlength="11" id="mobile">
            <span style="color: red;"></span>
        </p>
        <div class="bind-btn"><span class="true">确定</span></div>
    </div>
</div>
<script>
    $(function () {
        FastClick.attach(document.body);

        var isGuest = '<?= !$isLogin ?>';
        var promoTime = '<?= $promoTime ?>';
        var isBind = '<?= $isBind ?>';
        var isJoinWith = '<?= $isJoinWith ?>';

        if (isGuest) {
            if ('1' === promoTime) {
                note('活动未开始');
            } else if ('3' === promoTime) {
                note('活动已结束');
            } else {
                mobilePop();
            }
        } else {
            if (isJoinWith && !isBind) {
                bind();
            }
        }

        $('#mobile').on('keyup',function() {
            var num = moduleFn.clearNonum($(this));
        });

        $(".close").on("click",function() {
            $(".pomp,.mask,.pop").hide();
            $('body').off('touchmove');
        });

        var allowClick = true;

        $(".bind-btn").on("click",function() {
            if (!allowClick) {
                return;
            }

            if (validateMobile()) {
                allowClick = false;

                var key = '<?= $promo->key ?>';
                var phonenum =  $('#mobile').val();
                var xhr = $.get('/promotion/p1705/validate-mobile?key='+key+'&mobile='+phonenum);

                xhr.done(function(data) {
                    if (data.code) {
                        if ('undefined' !== typeof data.message && '' !== data.message) {
                            $('.tishi-p span').html(data.message);
                        }
                    } else {
                        $(".pomp,.mask,.pop").hide();
                        $('body').off('touchmove');
                    }

                    allowClick = true;
                });

                xhr.fail(function () {
                    note('系统繁忙,请稍后重试!');
                    allowClick = true;
                })
            }
        });

        $(".join-btn").on("click", function(e) {
            e.preventDefault();

            if (!allowClick) {
                return;
            }

            var xhr = $.get('/promotion/p1705/duobao');
            allowClick = false;

            xhr.done(function(data) {
                if (0 === data.code) {
                    var toUrl = '<?= Yii::$app->request->absoluteUrl ?>';

                    if ('undefined' !== typeof data.toUrl && '' !== data.toUrl) {
                        toUrl = data.toUrl;
                    }

                    location.replace(toUrl);
                } else if (1 === data.code) {
                    note('活动未开始');
                } else if (2 === data.code) {
                    note('活动已结束');
                } else if (3 === data.code) {
                    mobileFail();
                } else if (4 === data.code) {
                    invite();
                } else if (5 === data.code) {
                    login();
                }

                allowClick = true;
            });

            xhr.fail(function () {
                note('系统繁忙,请稍后重试!');
                allowClick = true;
            });
        });
    });

    function mobileFail() {
        $('.noChange,.mask').show();
        $('body').on('touchmove', eventTarget, false);
    }

    function login() {
        $('.login,.mask').show();
        $('body').on('touchmove', eventTarget, false);
    }

    function invite() {
        $('.invite,.mask').show();
        $('body').on('touchmove', eventTarget, false);
    }

    function bind() {
        $('.address,.mask').show();
        $('body').on('touchmove', eventTarget, false);
    }

    function note(msg) {
        $('.note p').html(msg);
        $('.note, .mask').show();
        $('body').on('touchmove', eventTarget, false);
    }

    function mobilePop() {
        $('.bing-info,.mask').show();
        $('body,html').scrollTop(0);
        $('body').on('touchmove', eventTarget, false);
    }

    function validateMobile() {
        var phonenum = $('#mobile').val();

        if (phonenum.length < 11) {
            $('.tishi-p span').html('请输入正确的手机号');

            return false;
        }

        if (!moduleFn.check.mobile(phonenum)) {
            $('.tishi-p span').html('手机号格式不正确');

            return false;
        }

        return true;
    }
</script>