<?php

$this->title = '夏至送好礼';
$this->share = $share;
$this->headerNavOn = true;
$currentUrl = Yii::$app->request->absoluteUrl;
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/xiazhi/css/index.css?v=2">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <div class="top-part"></div>
    <div class="middle-part"></div>
    <div class="bottom-part">
        <p class="rules">活动期间，累计年化投资每达到10万元，</p>
        <p class="rules">即可获得1个随机现金红包，最多可获得3个！</p>
        <p class="rules">现金奖励将直接发放到账户余额。</p>
        <div class="hongbao">
            <p class="shuliang">拥有<span><?= $restTicketCount ?></span>个</p>
            <div class="open-it">拆</div>
        </div>
        <div class="show-hongbao">我的红包</div>
        <a href="/deal/deal/index" class="go-invest">去理财</a>
        <p class="remind">本次活动最终解释权归楚天财富所有</p>
        <p class="remind">理财非存款 产品有风险 投资须谨慎</p>
    </div>
    <div class="mask"></div>
    <div class="have-hongbao">
        <p class="have-hongbao-title">我的红包</p>
        <ul class="have-hongbao-list">
            <?php foreach ($drawList as $key => $draw) :  ?>
            <?php
                if ($key > 2) {
                    break;
                }
            ?>
            <li>
                <span class="hongbao-number lf"><?= $draw->reward->name ?></span>
                <span class="hongbao-date rg"><?= date('Y-m-d  H:i:s', $draw->drawAt) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/xiazhi/images/btn_off.png" alt="" class="btn-off">
    </div>
    <div class="no-hongbao">
        <div class="tanhao">!</div>
        <p class="no-hongbao-content">您还没有红包哦</p>
        <p class="no-hongbao-content">快去投资吧！</p>
        <a href="/deal/deal/index" class="go-invest">去理财</a>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/xiazhi/images/btn_off.png" alt="" class="btn-off">
    </div>
    <div class="get-hongbao">
        <p class="get-hongbao-number"><span id="darwMoney"></span>&nbsp;&nbsp;元</p>
        <p class="get-hongbao-remind">现金已进入账户余额</p>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/xiazhi/images/btn_off2.png" alt="" class="btn-off">
    </div>
</div>

<script>
    function eventTarget (event) {
        var event = event || window.event;
        event.preventDefault();
    }
    $(function () {
        var requireJump = false;
        var restTicketCount = <?= $restTicketCount ?>;
        <?php if (null !== $user) { ?>
            //如果有拆红包的机会
            allowClick = true;
            $('.open-it').on('click',function (e) {
                if (!restTicketCount) {
                    $('.no-hongbao,.mask').show();
                    $('body').on('touchmove', eventTarget, false);
                    return false;
                }
                e.preventDefault();
                $(this).addClass('rotate');

                setTimeout(function () {
                    var xhr = $.get('/promotion/p1706/draw');
                    allowClick = false;

                    xhr.done(function(data) {
                        if (data.code === 0) {
                            $('#darwMoney').html(data.data.amount);
                            $('.open-it').removeClass('rotate');
                            $('.get-hongbao,.mask').show();
                            $('body').on('touchmove', eventTarget, false);
                            allowClick = true;
                            requireJump = true;
                        }
                    });
                    xhr.fail(function(jqXHR) {
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            var resp = $.parseJSON(jqXHR.responseText);
                            $('.open-it').removeClass('rotate');
                            allowClick = true;
                            if (1 === resp.code || 2 === resp.code) {
                                notice(resp.message);
                            } else if (3 === resp.code) {
                                $('.no-hongbao,.mask').show();
                                $('body').on('touchmove', eventTarget, false);
                                location.href = '/promotion/p1706/midsummer?_mark=' + Math.random() * 10000;
                            } else {
                                notice('系统繁忙，请稍后重试！');
                            }
                        } else {
                            notice('系统繁忙，请稍后重试！');
                        }
                    });
                },1800)
            });
        <?php } else { ?>
            $('.open-it').on('click',function () {
                location.href = "/site/login?next=<?= urlencode($currentUrl) ?>";
            });
        <?php } ?>

        <?php if (!empty($drawList)) { ?>
        //如果有红包记录
        $('.show-hongbao').on('click',function () {
            $('.have-hongbao,.mask').show();
            $('body').on('touchmove', eventTarget, false);
        });
        <?php } else { ?>
        //如果没有红包记录
            //判断是否为登录状态
            <?php if (null !== $user) { ?>
                if (restTicketCount) {
                    $('.show-hongbao').on('click',function () {
                        $('.have-hongbao,.mask').show();
                        $('body').on('touchmove', eventTarget, false);
                    });
                } else {
                    $('.show-hongbao').on('click',function () {
                        $('.no-hongbao,.mask').show();
                        $('body').on('touchmove', eventTarget, false);
                    });
                }
            <?php } else { ?>
                $('.show-hongbao').on('click',function () {
                    location.href = "/site/login?next=<?= urlencode($currentUrl) ?>";
                });
            <?php } ?>
        <?php } ?>

        $('.mask,.btn-off').on('click',function () {
            $('.have-hongbao,.no-hongbao,.get-hongbao,.mask').hide();
            $('body').off('touchmove');
            if (requireJump) {
                location.href = '/promotion/p1706/midsummer?_mark=' + Math.random() * 10000;
            }
        });
        function notice(msg) {
            var feBaseUrl = '<?= FE_BASE_URI ?>';
            var module = poptpl.popComponent({
                btnMsg : "确定",
                popMiddleHasDiv : true,
                popMiddleColor : "#fb5a1f",
                popBtmBackground:'#5f925a',
                popBorder:'0.10666667rem solid #5f925a',
                popBackground:'#e7ffe5',
                closeUrl:feBaseUrl + 'wap/campaigns/xiazhi/images/btn_off.png',
                contentMsg: msg,
                afterPop: function () {
                    location.href = '/promotion/p1706/midsummer?_mark=' + Math.random() * 10000;
                }
            },'close');
        }
    })
</script>