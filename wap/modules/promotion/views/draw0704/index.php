<?php

$this->title = '邀请好友送现金';
$this->share = $share;
$this->headerNavOn = true;

$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170706/css/index.css?v=1.11">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<div class="flex-content">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/banner.png" alt="" class="banner">

    <!-- 抽奖 -->
    <div class="draw-module">
        <div class="draw-module-top">
            <?php if (1 === $promoStatus) : ?>
                <a href="javascript:unstart();" class="my-prize">我的奖品</a>
            <?php else : ?>
                <?php if (is_null($user)) : ?>
                    <a href="javascript:login();" class="my-prize">我的奖品</a>
                <?php else : ?>
                    <?php if (empty($drawList)) : ?>
                        <a href="javascript:prizeList(false);" class="my-prize">我的奖品</a>
                    <?php else : ?>
                        <a href="javascript:prizeList(true, '<?= $drawList[0]->reward->name ?>', '<?= $drawList[0]->reward->path ?>');" class="my-prize">我的奖品</a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <p class="draw-rule">活动期间成功邀请好友注册并实名认证，即可获得一次抽奖机会哦（最多1次）！</p>
        </div>
        <div class="draw-module-bottom">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/draw-prize-01.png" alt="">
            <ul class="prize-top clearfix">
                <li>代金券</li>
                <li>现金红包</li>
                <li>积分</li>
                <li>勺筷礼盒</li>
            </ul>
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/draw-prize-02.png" alt="">
            <ul class="prize-bottom clearfix">
                <li>空气加湿器</li>
                <li>超市卡</li>
                <li>苹果手表</li>
                <li>苹果手机</li>
            </ul>
        </div>

        <!-- 按钮 -->
        <?php if (1 === $promoStatus) : ?>
            <a href="javascript:unstart();" class="btn btn-invite">邀请好友</a>
            <a href="javascript:unstart();" class="btn btn-draw">点击抽奖</a>
        <?php else : ?>
            <?php if (is_null($user)) : ?>
                <a href="javascript:login();" class="btn btn-invite">邀请好友</a>
                <a href="javascript:login();" class="btn btn-draw">点击抽奖</a>
            <?php else : ?>
                <a href="/user/invite" class="btn btn-invite">邀请好友</a>
                <?php if (empty($drawList)) : ?>
                    <a href="javascript:void(0);" class="btn btn-draw" id="draw">点击抽奖</a>
                <?php else : ?>
                    <a href="javascript:void(0);" class="btn btn-draw" style="background: #999999;">点击抽奖</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!-- 邀请 -->
    <div class="invite-box">
        <h4><span>已邀请投资：<i><?= $inviteCount ?></i> 位</span></h4>
        <ul>
            <li>活动期间，成功邀请</li>
            <li>2位好友注册并投资，送<span>28元</span>现金红包;</li>
            <li>5位好友注册并投资，送<span>88元</span>现金红包;</li>
            <li>10位好友注册并投资，送<span>188元</span>现金红包!</li>
            <li class="txt-light">红包奖励可叠加！</li>
            <li class="txt">比如：邀请10位好友注册并投资，即可获得28+88+188=304元现金红包!</li>
        </ul>
        <p class="tip">温馨提示：被邀请人在活动期间完成注册，邀请人才能获得奖励哦！奖品将在活动结束后7个工作日内发放(账户需实名认证)。</p>
    </div>

    <!-- 送现金 -->
    <div class="cash-box">
        <h4></h4>
        <ul>
            <li>邀请人可以获得被邀请人前三笔投资<span>0.1%</span>的返现奖励哦！</li>
            <li>比如：被邀请人前三笔累积投资<span>50万元</span>，邀请人可以获得<span>500元</span>现金奖励！</li>
            <li class="tips">温馨提示：邀请好友前必须在温都金服平台投资过，才能获得奖励哦！</li>
        </ul>

        <?php if (1 === $promoStatus) : ?>
            <a href="javascript:unstart();" class="get-cash">领走现金</a>
        <?php else : ?>
            <?php if (is_null($user)) : ?>
                <a href="javascript:login();" class="get-cash">领走现金</a>
            <?php else : ?>
                <a href="/user/invite" class="get-cash">领走现金</a>
            <?php endif; ?>
        <?php endif; ?>

        <p>本次活动最终解释权归温都金服所有</p>
        <p>所有活动及奖品与苹果公司无关</p>
    </div>
</div>
<script>
    $(function() {
        //点击进行抽奖
        var allowClick = true;
        $('#draw').on('click', function(e) {
            e.preventDefault;

            if(!allowClick) {
                return false;
            }

            var xhr = $.get('/promotion/draw0704/draw');
            allowClick = false;

            xhr.done(function(data) {
                if (data.code === 0) {
                    prizeList(true, data.prize.name, data.prize.imageUrl, function () {
                        location.href = '';
                        allowClick = true;
                    });
                }
            });

            xhr.fail(function(jqXHR) {
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);
                    if (1 === resp.code) {
                        goInviteNotice('您还没有成功邀请好友哦！');
                    } else if (2 === resp.code) {
                        goInviteNotice('您已经没有抽奖机会了哦！快去邀请好友，领取其他现金奖励吧！');
                    } else {
                        toastCenter(resp.message);
                    }
                } else {
                    toastCenter('系统繁忙，请稍后重试！');
                }

                allowClick = true;
            });
        });
    });

    function prizeList(hasPrize, prizeName, prizeImgUrl, func) {
        if (hasPrize) {
            var module = poptpl.popComponent({
                closeHas : true,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/pop_close.png",
                popTopHasDiv : true,
                title: '恭喜您获得'+prizeName+'！',
                popTopColor : "#ff4040",
                popTopFontSize : "0.4rem",
                popTopHasImg : true,
                popTopImg : '<?= FE_BASE_URI ?>'+prizeImgUrl,
                popMiddleHasDiv : true,
                popMiddleColor : "#fb5a1f",
                popBackground : "#e1f8ff",
                popBorder: "0.10666667rem solid #82cee7",
                popBtmBackground : "#82cee7",
                popBtmFontSize : ".48rem",
                contentMsg:"<span style='font-size: 0.4rem;color: #333;line-height: .4rem!important;'>邀请好友投资<br>还能获得大量现金奖励哦！</span>",
                btnHref : "/user/invite",
                btnMsg : "继续邀请",
                afterPop: function () {
                    func();
                }
            }, 'close');
        } else {
            goInviteNotice('您还没有获得奖品哦，快去邀请好友吧！');
        }
    }

    function goInviteNotice(msg) {
        var module = poptpl.popComponent({
            closeHas : true,
            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/pop_close.png",
            btnMsg : "去邀请",
            popTopHasImg : true,
            popTopImg : "<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/reminder-icon.png",
            popTopHasDiv : false,
            popMiddleHasDiv : true,
            popMiddleColor : "#fb5a1f",
            popBackground : "#e1f8ff",
            popBorder: "0.10666667rem solid #82cee7",
            popBtmBackground : "#82cee7",
            popBtmFontSize : ".48rem",
            contentMsg:"<span style='display:inline-block;padding-bottom:.37rem;font-size: 0.4rem;color: #333;'>"+msg+"</span>",
            btnHref : "/user/invite"
        }, 'close');
    }

    <?php if (is_null($user)) : ?>
        function login() {
            var module = poptpl.popComponent({
                closeHas : true,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/pop_close.png",
                btnMsg : "去登录",
                popTopHasImg : true,
                popTopImg : "<?= FE_BASE_URI ?>wap/campaigns/active20170706/images/reminder-icon.png",
                popTopHasDiv : false,
                popMiddleHasDiv : true,
                popMiddleColor : "#fb5a1f",
                popBackground : "#e1f8ff",
                popBorder: "0.10666667rem solid #82cee7",
                popBtmBackground : "#82cee7",
                popBtmFontSize : ".48rem",
                contentMsg:"<span style='display:inline-block;padding-bottom:.37rem;font-size: 0.4rem;color: #333;'>您还没有登录哦！</span>",
                btnHref : "<?= $loginUrl ?>"
            }, 'close');
        }
    <?php endif; ?>

    <?php if (1 === $promoStatus) : ?>
        function unstart() {
            toastCenter('活动未开始');
        }
    <?php endif; ?>
</script>