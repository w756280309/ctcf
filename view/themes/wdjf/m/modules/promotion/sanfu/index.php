<?php

$this->title = '翻牌子抽大奖';
$this->headerNavOn = true;

$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170711/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js?v=1.0"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script type="text/javascript">
    var linkUrl = '<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/sanfu/';
    var imgUrl = '<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/wxshare.png';
    var appId = '<?= Yii::$app->params['weixin']['appId'] ?>';
    var feBaseUri = '<?= FE_BASE_URI ?>';
</script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/js/share.js"></script>

<div class="flex-content">
    <div class="banner">
        <div class="banner_01"></div>
        <div class="banner_02"></div>
        <div class="banner_03">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/leaves_01.png" alt="leaves">
            <p>剩余翻牌次数<span><?= $restCount ?></span>次</p>
            <div class="prizes">我的奖品</div>
        </div>
    </div>
    <!-- 抽奖 -->
    <div class="draw-module">
        <div class="gift-show">
            <ul>
                <li class="lf">
                    <div class="gift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fruit_02.png"></div>
                    <div class="noGift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fanpai_01.png"></div>
                </li>
                <li class="lf">
                    <div class="gift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fruit_03.png"></div>
                    <div class="noGift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fanpai_01.png"></div>
                </li>
                <li class="lf">
                    <div class="gift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fruit_01.png"></div>
                    <div class="noGift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fanpai_01.png"></div>
                </li>
                <li class="lf">
                    <div class="gift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fruit_04.png"></div>
                    <div class="noGift"><img alt="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fanpai_01.png"></div>
                </li>
                <li class="shuffle">洗牌中...</li>
            </ul>
        </div>
        <div class="flop">翻个牌子</div>
        <div class="flopUpgrade">升级奖池</div>
    </div>
    <!-- 规则 -->
    <div class="invite-box">
        <div class="regular">活动规则</div>
        <ol>
            <li>活动时间：2017年7月17日-7月23日；</li>
            <li>每日获得1次免费翻牌机会，分享本页面（必须分享至朋友圈）后再获得1次机会；</li>
            <li>活动期间每日年化投资满10万元，将获得1次翻金牌机会，此次奖池将自动升级为更高价值礼品；</li>
            <li>未使用的次数不能累积到次日；</li>
            <li>每次翻牌必定获得礼品1份，翻中西瓜牌将额外获得1000元超市卡一张！</li>
        </ol>
        <div class="tips">注：实物礼品将于活动结束后7个工作日内发放</div>

        <div class="share invite-btn"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/share.png" alt="">点击分享</div>
    </div>
</div>

<!--对应的奖品列表-->
<div class="prizes-box">
    <div class="outer-box">
        <img class="pop_close" src="<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png" alt="">
        <div class="prizes-pomp">
            <div id="wrapper">
                <ul>
                    <li class="prizes-title">我的奖品</li>
                    <?php if (empty($drawList)) : ?>
                        <li class="no-prizes">您还没有获得奖品哦！</li>
                    <?php else : ?>
                        <?php foreach ($drawList as $list) : ?>
                            <li class="clearfix">
                                <div class="lf"><img src="<?= FE_BASE_URI.$list->reward->path ?>" alt="礼品"></div>
                                <div class="lf">
                                    <p><?= $list->reward->name ?></p>
                                    <p>中奖时间: <?= date('Y-m-d', $list->drawAt) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        FastClick.attach(document.body);

        var myScroll = new iScroll('wrapper', {
            vScrollbar:false,
            hScrollbar:false
        });

        //我的奖品按钮
        $(".prizes").on("click", function() {
            if (preValidate()) {
                $('.prizes-box').show();
                $('body').on('touchmove',eventTarget, false);
            }

            myScroll.refresh();//点击后初始化iscroll
        });

        //升级奖池
        $('.flopUpgrade').on("click", function () {
            if (preValidate()) {
                requireRaise();
            }
        });

        $('.prizes-box').on('click',function() {
            $(this).hide();
            $('body').off('touchmove');
        });

        $('#wrapper').on('click',function(event) {
            event.stopPropagation();
        });

        //点击翻牌按钮开始翻牌动画
        $('.flop').on('click', function () {
            if (preValidate()) {
                anim();
            }
        });

        <?php if ($requireRaise) : ?>
            $('.noGift img').attr({src:"<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fanpai_02.png"});
        <?php endif; ?>
    });

    function eventTarget(event) {
        var event = event || window.event;
        event.preventDefault();
    }

    function preValidate()
    {
        var promoStatus = '<?= $promoStatus ?>';
        var isLogin = '<?= !is_null($user) ?>';

        if ('1' === promoStatus) {
            toastCenter('活动未开始');

            return false;
        }

        if (!isLogin) {
            unLogin();

            return false;
        }

        return true;
    }

    function unLogin() {
        //登录
        var module = poptpl.popComponent({
            popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
            popBorder:0,
            closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
            btnMsg : "去登录",
            popTopColor:"#fb3f5a",
            title:'<p style="font-size:0.50666667rem; margin: 1rem 0 2.2rem;">您还未登录哦！</p>',
            popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
            popBtmBorderRadius:0,
            popBtmFontSize : ".50666667rem",
            btnHref:'<?= $loginUrl ?>'
        });
    }

    function requireRaise() {
        <?php if (!$requireRaise) : ?>
            var module = poptpl.popComponent({
                popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                popBorder:0,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                btnMsg : "去投资",
                popMiddle:false,
                popTopColor:"#fb3f5a",
                title:'<p style="font-size:0.50666667rem;margin: 1rem 0 1.5rem;">每日年化投资十万元<br>即可升级奖池哦！</p>',
                popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                popMiddleHasDiv:true,
                popBtmBorderRadius:0,
                popBtmFontSize : ".50666667rem",
                btnHref: '/deal/deal'
            });
        <?php else : ?>
            var module = poptpl.popComponent({
                popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                popBorder:0,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                popMiddle:false,
                popTopColor:"#fb3f5a",
                title:'<p style="font-size:0.50666667rem;margin: 1.2rem 0 0.3rem;">每日年化投资十万元，<br>即可升级奖池</p><p style="color: #21b1e0;font-size: 0.4rem;margin-bottom: 0.8rem;">您今天已经升级过了哦！</p>',
                popBtmBackground:'url(img/pop_btn_01.png) no-repeat',
                popMiddleHasDiv:true,
                popBtmHas:false
            });
        <?php endif; ?>
    }

    //翻拍动画js
    function anim() {
        $(this).off('click');
        $('.shuffle').show();
        $('.gift').addClass('firstChild');
        $('.noGift').addClass('lastChild');
        $('.gift-show ul li').each(function(){
            var index = $(this).index();
            $('.gift-show ul li').eq(index).addClass('animate'+index);
        });
        $("#giftBox").addClass('animate4');

        setTimeout(function() {
            $('.shuffle').hide();
            $('.gift-show ul li').each(function(){
                var index = $(this).index();
                $('ul li').eq(index).removeClass('animate'+index);
            });
            $("#giftBox").removeClass('animate4');
            $('.gift-show ul li').on('click',function() {
                var this_ = $(this);
                draw(this_);
            })
        }, 3000)
    }

    function draw(this_)
    {
        var xhr = $.get('/promotion/sanfu/draw');

        xhr.done(function(data) {
            if (data.code === 0) {
                var giftNum = parseInt(Math.random()*3+2);
                this_.find('.gift img').attr({src:"<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/fruit_0"+giftNum+".png"})
                this_.find('.gift').toggleClass('firstChild');
                this_.find('.noGift').toggleClass('lastChild');
                $('.gift-show ul li').off('click');

                var module = poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg.png) no-repeat',
                    popBorder: 0,
                    closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                    btnMsg : "收下礼品",
                    popTopColor: "#fad675",
                    bgSize: "100% 100%",
                    title: '<p style="font-size:0.666667rem;text-shadow: 1px 1px 2px #947e6a;">恭喜您获得</p><p style="font-size:0.4266667rem;text-shadow: 1px 1px 1px #947e6a;">'+data.data.name+'</p>',
                    popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                    popMiddleHasDiv:true,
                    contentMsg: "<img style='margin:0 auto;display: block;width: 4.2666667rem;' src='<?= FE_BASE_URI ?>"+data.data.imageUrl+"' alt=''/>",
                    popBtmBorderRadius:0,
                    popBtmFontSize : ".50666667rem",
                    afterPop: function () {
                        location.href = '/promotion/sanfu/?_mark=<?= time() ?>';
                    }
                }, 'close');
            }
        });

        xhr.fail(function(jqXHR) {
            if (400 === jqXHR.status && jqXHR.responseText) {
                var resp = $.parseJSON(jqXHR.responseText);
                if (1 === resp.code || 2 === resp.code) {
                    toastCenter(resp.message);
                } else if (3 === resp.code) {
                    unLogin();
                } else if (4 === resp.code) {
                    var module = poptpl.popComponent({
                        popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                        popBorder:0,
                        closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                        btnMsg : "去分享",
                        popMiddle:false,
                        popTopColor:"#fb3f5a",
                        title:'<p style="font-size: 0.50666667rem; margin: 1rem 0 0.3rem;">您的抽奖机会已用完！</p><p style="color: #21b1e0;font-size: 0.4rem;margin-bottom: 1.4rem;">分享页面还能再抽<span style="color: #ffc155;">1</span>次 </p>',
                        popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                        popMiddleHasDiv:true,
                        popBtmBorderRadius:0,
                        popBtmFontSize : ".50666667rem",
                    }, function() {
                        $('.share').trigger('click');
                        poptpl.confirmBtn();
                        closeShare();
                    });
                } else if (5 === resp.code) {
                    var module = poptpl.popComponent({
                        popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                        popBorder:0,
                        closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                        btnMsg : "去投资",
                        popMiddle:false,
                        popTopColor:"#fb3f5a",
                        title:'<p style="font-size:0.50666667rem;margin: 1rem 0 0.3rem;">您的抽奖机会已用完！</p><p style="color: #21b1e0;font-size: 0.4rem;margin-bottom: 0.8rem;">年化投资十万元即可再抽<span style="color: #ffc155;">1</span>次 ，奖池更丰富！</p>',
                        popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                        popMiddleHasDiv: true,
                        popBtmBorderRadius: 0,
                        popBtmFontSize : ".50666667rem",
                        btnHref:'/deal/deal'
                    });
                } else if (6 === resp.code) {
                    var module = poptpl.popComponent({
                        popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                        popBorder: 0,
                        closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                        btnMsg : "确 定",
                        popMiddle:false,
                        popTopColor:"#fb3f5a",
                        title:'<p style="font-size:0.50666667rem;margin: 1rem 0 0.3rem;">您的抽奖机会已用完！</p><p style="color: #21b1e0;font-size: 0.4rem;margin-bottom: 1.4rem;">明天再来吧！ </p>',
                        popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                        popMiddleHasDiv:true,
                        popBtmBorderRadius:0,
                        popBtmFontSize : ".50666667rem",
                    });
                } else {
                    toastCenter('系统繁忙，请稍后重试！', function () {
                        location.href = '/promotion/sanfu/?_mark=<?= time() ?>';
                    });
                }
            } else {
                toastCenter('系统繁忙，请稍后重试！', function () {
                    location.href = '/promotion/sanfu/?_mark=<?= time() ?>';
                });
            }
        });
    }
</script>
