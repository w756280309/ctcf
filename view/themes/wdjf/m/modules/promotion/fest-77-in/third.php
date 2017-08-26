<?php

$this->title = '七夕闯关作战';

$topBgUrl = $waitAwarded || $isFinishedThree ? 'third-color-status.png' : 'third-grey-status.png';
$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);
$list = [];
foreach ($awardList as $k=>$award) {
    $list[$k]['gifts_num'] = FE_BASE_URI.$award['path'];
    $list[$k]['gifts_title'] = $award['name'];
    $list[$k]['gifts_time'] = $award['note'];
}
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/gifts-list.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/third.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<div class="flex-content">
    <div class="banner-top">
        <a href="javascript:;" class="active-rule">查看规则>></a>
        <?php if (null === $user) { ?>
            <a href="<?= $loginUrl ?>" class="go-login">点击登录>></a>
        <?php } ?>
            <div class="img-status" style="background: url('<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/<?= $topBgUrl ?>') no-repeat 0 0;background-size: 100% 100%;"></div>
        <?php if ($waitAwarded) { ?>
            <a href="javascript:;" class="a-btn btn-light btn-open">领取奖励</a>
        <?php } elseif ($isFinishedThree) { ?>
            <a href="javascript:;" class="a-btn btn-light btn-my-prize">查看奖励</a>
        <?php } else { ?>
            <a href="/deal/deal/index" class="a-btn btn-grey btn-invited" style="display: inline-block">去完成新用户首投</a>
            <a href="/user/invite" class="a-btn btn-grey btn-invest" style="display: inline-block">邀请好友首投</a>
        <?php } ?>
    </div>
    <div class="text-tips">
        <p>达成条件：活动期间完成新用户首次投资，或邀请好友完成新用户首投</p>
        <p>达成奖励：七夕大礼包一份，内含<span>7.7元现金红包</span>和<span>随机高价值礼品</span>一份！</p>
        <a href="javascript:;" class="a-prize-btn">
            <img class="finger" src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-finger.png">
        </a>
    </div>
    <div class="page-bottom"></div>
    <!--  奖池 -->
    <div class="prizes-pond">
        <div class="prizes-pond-inner">
            <a href="javascript:;" class="btn-close"></a>
            <p>七夕奖池</p>
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-prizes-top.png" alt="">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-prizes-bottom.png" alt="">
        </div>
    </div>
    <!--  活动规则 -->
    <div class="mask" style="display: none"></div>
    <div class="rule-box" style="display: none;">
        <h5>活动规则</h5>
        <img class="close-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/close.png" alt="">
        <ol>
            <li>活动时间2017年8月28日-8月31日；</li>
            <li>活动期间，完成新用户首次投资，或邀请好友完成新用户首次投资，即可点亮牛郎织女图（以认购成功时间为准）；</li>
            <li>点亮牛郎织女图，可领取七夕大礼包一份，内含7.7元现金红包（必得）和随机高价值礼品一份；</li>
            <li>现金红包、代金券将在领取后立即到账，实物礼品将于7个工作日内联系发放，如有疑问请联系客服电话400-101-5151。</li>
        </ol>
    </div>
</div>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/js/gifts-list.js"></script>
<script>
    giftsList({
        isGifts:Boolean(<?= !empty($awardList) ?>),//有奖品，无奖品为false
        closeImg:'<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-close.png',
        list:<?= json_encode($list) ?>
    });

    $(function(){
        FastClick.attach(document.body);
        var myScroll = new iScroll('wrapper',{
            vScrollbar:false,
            hScrollbar:false
        });
        // 规则
        $(".active-rule").click(function () {
            $(".mask ,.rule-box").show();
            $('body').on('touchmove',eventTarget, false);
        });
        $(".close-btn").click(function () {
            $(".mask ,.rule-box").hide();
            $('body').off('touchmove');
        });
        // 奖池
        $('.a-prize-btn').on('click',function() {
            $('.prizes-pond').show();
            $('body').on('touchmove',eventTarget, false);
        });
        $('.btn-close').on('click',function() {
            $('.prizes-pond').hide();
            $('body').off('touchmove');
        });

        //我的奖品按钮
        $(".btn-my-prize").on("click",function(){
            $('.prizes-box').show();
            $('body').on('touchmove',eventTarget, false);
            myScroll.refresh();//点击后初始化iscroll
        });

        $('.pop_close').on('click',function(){
            $('.prizes-box').hide();
            $('body').off('touchmove');
        });
        function eventTarget(event) {
            var event = event || window.event;
            event.preventDefault();
        }
        $('.btn-open').on('click', function(){
            draw('btn-open','/promotion/fest-77-in/draw?key=promo_170828', function(){
                var xhr = $.get('/promotion/fest-77-in/award-three');
                xhr.done(function (data) {});
            });
        });

        function draw(btn, url, callback) {
            var _btn = $(btn);
            if (_btn.hasClass('hasClicked')) {
                return false;
            }
            _btn.addClass('hasClicked');
            var xhr = $.get(url);
            xhr.done(function (data) {
                _btn.removeClass('hasClicked');
                if (data.code === 0) {
                    setTimeout(function () {
                        if (callback) {
                            callback();
                        }
                        var prizeImg = '<?= FE_BASE_URI ?>'+data.ticket.path; // 奖品图片
                        var prizeName =  '7.7元红包+'+data.ticket.name;
                        var module = poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-prize-bg.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-close.png",
                            btnMsg : "收下礼品",
                            popTopColor:"#fff",
                            bgSize:"100% 100%",
                            title:'<p style="font-size:0.72rem;">恭喜您获得了</p><p style="font-size:0.5066667rem;padding: 0;text-align: center">'+prizeName+'</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/pop_btn_01.png) no-repeat',
                            popMiddleHasDiv:true,
                            contentMsg:"<img style='margin: -0.9rem 0 .8rem 3.6rem;display: block;width: 2.50666667rem;' src='"+prizeImg+"' alt=''/>",
                            popBtmBorderRadius:0,
                            popBtmFontSize : ".50666667rem",
                            afterPop: function () {
                                location.href = '/promotion/fest-77-in/third?_mark=<?= time() ?>';
                            }
                        },'close');
                    }, 1000);
                }
            });

            xhr.fail(function(jqXHR) {
                _btn.removeClass('hasClicked');
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);
                    if (1 === resp.code || 2 === resp.code) {
                        toastCenter(resp.message);
                    } else if (3 === resp.code) {
                        var module = poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/pop_bg_02.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-close.png",
                            btnMsg : "去登录",
                            popTopColor:"#fb3f5a",
                            title:'<p style="font-size:0.60666667rem; margin: 1rem 0 2.2rem;">您还未登录哦！</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/pop_btn_02.png) no-repeat',
                            popBtmBorderRadius:0,
                            popBtmFontSize : ".50666667rem",
                            btnHref:'<?= $loginUrl ?>'
                        });
                    } else if (4 === resp.code || 5 === resp.code || 6 === resp.code) {
                        var module = poptpl.popComponent({
                            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/pop_bg_02.png) no-repeat',
                            popBorder:0,
                            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/third-pop-close.png",
                            btnMsg : "确认",
                            popMiddle:false,
                            popTopColor:"#fb3f5a",
                            title:'<p style="font-size: 0.60666667rem; margin: 1rem 0 0.3rem;">您已经抽过奖了！</p>',
                            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/pop_btn_02.png) no-repeat',
                            popMiddleHasDiv:true,
                            popBtmBorderRadius:0,
                            popBtmFontSize : ".50666667rem",
                        }, 'close');
                    } else {
                        toastCenter('系统繁忙，请稍后重试！', function () {
                            location.href = '/promotion/fest-77-in/third?_mark=<?= time() ?>';
                        });
                    }
                } else {
                    toastCenter('系统繁忙，请稍后重试！', function () {
                        location.href = '/promotion/fest-77-in/third?_mark=<?= time() ?>';
                    });
                }
            });
        }
    });
</script>