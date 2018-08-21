<?php

use common\utils\StringUtils;

$this->title = '520周年庆';
$this->share = $share;
$this->headerNavOn = true;

$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170520/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content">
    <img class="client-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/banner.png" alt="banner">
    <img class="client-img title-01" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/title-01.png" alt="title-01">
    <p class="title-01-text">楚天财富周年庆特别版出借项目<br/>外企航服定向融资工具，限时加息<span>0.5%</span></p>
    <p class="box">国企+央企双担保<br/>原预期年化收益7.4%-8.0%，再加<span>0.5%</span></p>
    <a class="invest" href="/deal/deal/index">去出借</a>
    <img class="client-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/title-02.png" alt="title-02">
    <p class="invest-txt">5月20日当天，出借任意项目（不含转让产品），所获积分提升为原来的5倍。</p>
    <img class="client-img invest-client" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/table.png" alt="table">
    <a class="invest invest-02" href="/deal/deal/index">去出借</a>
    <img class="client-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/title-03.png" alt="title-03">
    <p class="invest-txt">1枚勋章可以兑换1个现金红包，最高520元！<br/>现金直接进入您的出借账户，快来试试手气吧！</p>
    <div class="box-red-packet">
        <img class="client-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/red-packet.png" alt="红包">
        <?php if (null === $user) : ?>
            <a class="my-red-packet" href="<?= $loginUrl ?>"></a>
        <?php else : ?>
            <a class="my-red-packet"></a>
        <?php endif; ?>
        <span class="num" ><?= $tickets ?></span>
        <?php if ($promoStatus) : ?>
            <a class="prize invalid" style="background-color: gray;"><?= 1 === $promoStatus ? '活动未开始' : '活动已结束' ?></a>
        <?php elseif (null === $user) : ?>
            <a class="prize" href="<?= $loginUrl ?>">兑红包</a>
        <?php else: ?>
            <a class="prize">兑红包</a>
        <?php endif; ?>
    </div>
    <img class="client-img task" src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/task.png" alt="task">
    <p class="tips">本次活动最终解释权归楚天财富所有<br/>产品有风险 出借须谨慎</p>

    <!-- 遮罩 -->
    <div class="mask"></div>

    <!-- 勋章不足 -->
    <div class="flip short-prize">
        <a class="close" href="javascript:;"></a>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/prize-icon.png" alt="">
        <p>您的勋章数量不足<br/>快去出借赚取5倍积分吧！</p>
        <a class="btn" href="/deal/deal/index">赚积分</a>
    </div>

    <!-- 红包 -->
    <div class="flip red-packet-prize">
        <a class="red-packet-btn" href="javascript:;">
            <img class="<?= $tickets > 0 ? '' : 'clickFlag' ?>" width='100%' src="<?= FE_BASE_URI ?>wap/campaigns/active20170520/images/chai-red-packet.png" alt="">
        </a>
    </div>

    <!-- 拆红包 -->
    <div class="flip money-prize">
        <a class="close money-close" href="javascript:;"></a>
        <span class="money"></span>
    </div>

    <?php if (!empty($drawList)) : ?>
        <!-- 中奖记录 -->
        <div class="flip my-record draw-list">
            <a class="close" href="javascript:;"></a>
            <p>我的红包</p>
            <ul class="list">
                <?php foreach ($drawList as $list) : ?>
                    <li><i class="packet-money"><?= StringUtils::amountFormat2($list->reward->ref_amount) ?>元</i><span><?= date('Y-m-d', $list->drawAt) ?></span><i><?= date('H:i:s', $list->drawAt) ?></i></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <!-- 无红包 -->
        <div class="flip no-prize draw-list">
            <a class="close" href="javascript:;"></a>
            <p>Sorry！您还没有红包！<br/>请使用勋章兑换红包！</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($user) : ?>
    <script>
        $(function() {
            FastClick.attach(document.body);
            forceReload_V2();

            // 我的红包
            $('.my-red-packet').on('click',function() {
                $('.draw-list').show();
                $('.mask').show();
                $('body').on('touchmove', eventTarget, false);
            });

            // 兑红包
            $('.prize').on('click',function() {
                var invalid = $(this).hasClass('invalid');
                if (invalid) {
                    return;
                }

                $('.mask').show();
                $('body').on('touchmove', eventTarget, false);
                // 勋章不够
                if ($('.red-packet-btn img').hasClass('clickFlag')) {
                    $('.short-prize').fadeIn(200);
                    return false;
                }
                $('.red-packet-prize').show();
            });

            // 拆红包
            var allowClick = true;
            $('.red-packet-prize').on('click',function() {
                var reaPacket = $('.red-packet-btn img');
                if (reaPacket.hasClass('clickFlag')) {
                    return;
                }

                var invalid = $('.prize').hasClass('invalid');
                if (invalid) {
                    return;
                }

                reaPacket.addClass('transform-start');
                reaPacket.addClass('clickFlag');

                if (!allowClick) {
                    return;
                }

                var xhr = $.get('/promotion/p1705/draw520');
                allowClick = false;

                xhr.done(function(data) {
                    if (0 === data.code) {
                        $('.money-prize span').html(data.drawAmount);

                        setTimeout(function () {
                            $('.red-packet-prize').fadeOut(200);
                            $('.money-prize').fadeIn(200);
                            reaPacket.removeClass('transform-start');
                            allowClick = true;
                        }, 500);
                    } else {
                        allowClick = true;
                    }
                });

                xhr.fail(function(jqXHR) {
                    reaPacket.removeClass('transform-start');
                    $('body').off('touchmove');
                    alert('网络异常，请刷新重试！');
                    location.href = '';
                    allowClick = true;
                });
            });

            $('.short-prize .close').on('click',function() {
                $('.short-prize').hide();
                $('.mask').hide();
                $('body').off('touchmove');
            });

            $('.no-prize .close').on('click',function() {
                $('.no-prize').hide();
                $('.mask').hide();
                $('body').off('touchmove');
            });

            $('.money-prize .close').on('click',function() {
                $('.money-prize').hide();
                $('.mask').hide();
                location.href = '';
                $('body').off('touchmove');
            });

            $('.my-record .close').on('click',function() {
                $('.my-record').hide();
                $('.mask').hide();
                $('body').off('touchmove');
            });
        });
    </script>
<?php endif; ?>