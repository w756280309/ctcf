<?php

$this->title = '砸金蛋赢大奖';
$this->share = $share;

$loginUrl = '/site/login?next='.urlencode(Yii::$app->request->absoluteUrl);

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/css/index.css?v=20170407">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/js/index.js"></script>

<div style="display: none">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/weitu.png" alt="" width="100%"/>
</div>
<div class="container">
    <a href="#" class="my_prize"></a>
    <!--banner-box-->
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/top-banner.png" alt="">
        </div>
    </div>
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/banner1.png" alt="">

            <?php $nowDate = date('Y-m-d H:i:s'); ?>
            <?php if ($promo->startTime > $nowDate || !$promo->isOnline) { ?>
                <div class="banner-opportunity f17">活动未开始</div>
            <?php } elseif ($promo->endTime && $promo->endTime < $nowDate) { ?>
                <div class="banner-opportunity f17">活动已结束</div>
            <?php } elseif (\Yii::$app->user->isGuest) { ?>
                <div class="banner-opportunity f17"><a href='<?= $loginUrl ?>' style='color:white;'>登录马上砸蛋</a></div>
            <?php } else { ?>
                <div class="banner-opportunity f17">您还有<span><?= $restTicket ?>次机会</span></div>
            <?php } ?>

            <ul class="banner-inner">
                <li>
                    <div>
                        <img class="banner-egg" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/egg.png" alt="">
                        <img class="banner-lie0" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/hua.png" alt="">
                        <img class="banner-chui" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/chuizi.png" alt="">
                    </div>
                </li>
                <li>
                    <div>
                        <img class="banner-egg" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/egg.png" alt="">
                        <img class="banner-lie0" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/hua.png" alt="">
                        <img class="banner-chui" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/chuizi.png" alt="">
                    </div>
                </li>
                <li>
                    <div>
                        <img class="banner-egg" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/egg.png" alt="">
                        <img class="banner-lie0" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/hua.png" alt="">
                        <img class="banner-chui" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/chuizi.png" alt="">
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!--award-box-->
    <div class="row award-box">
        <div class="col-xs-12">
            <img class="award-title" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/title.png" alt="">
            <div class="award-inner">
                <div class="award-content">
                    <ul class="clearfix">
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-1.jpg" alt="">
                                <p class="f11">iPhone 7 Plus</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-2.jpg" alt="">
                                <p class="f11">iPad mini 4</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-3.jpg" alt="" style="width: 90%;">
                                <p class="f11">京东E卡</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-4.png" alt="">
                                <p class="f11">迪士尼保温杯</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-5.jpg" alt="" style="width: 90%;">
                                <p class="f11">沃尔玛超市卡</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-6.jpg" alt="">
                                <p class="f11">进口纯燕麦片</p>
                            </div>
                        </li>
                        <li style="width: 100%;height:1px;"></li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-7.png" alt="" style="width: 90%;height: 1.2rem;margin-top: 0.1rem;">
                                <p class="f11">积分</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-8.png" alt="">
                                <p class="f11">LG竹盐牙膏</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/prize-9.png" alt="">
                                <p class="f11">东北黑香米</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <img class="award-bottom" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/bottom.png" alt="">
            <div class="ruler-box">
                <div class="ruler-title f15" style="margin-bottom: 0.173rem">活动规则：</div>
                <ul class="f12">
                    <li>活动期间所有注册用户均可获得1次砸蛋机会；</li>
                    <li>活动期间用户每投资2万元（不含转让产品），可获得1次砸蛋机会，上不封顶；</li>
                    <li>活动时间2017年4月10日-4月12日，过期未使用的砸蛋机会将失效；</li>
                    <li>在活动页面右上角“我的奖品”中可以查看已获得的奖品；</li>
                    <li>活动结束后7个工作日内，工作人员将与您联系确认领取奖品相关事宜，请保持通讯畅通。</li>
                </ul>
                <div class="ruler-bottom f12">注：本活动最终解释权归温都金服所有。</div>
                <div class="remind-bottom f9">理财非存款 产品有风险 投资须谨慎</div>
            </div>
        </div>
    </div>
    <!--login-box-->
    <div class="mark-box"></div>
    <div class="login-box">
        <img class="close-img" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/close.png" alt="">
        <div class="login-img">
            <!--nologin-->
            <img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/noChange.png" alt="">
        </div>
        <!--蛋壳太硬了！登录后再砸吧！-->
        <div class="login-inner f14">没有砸蛋机会了~ 快去投资吧！</div>
        <a href="javascript:;" class="login-btn"><img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/btn.png" alt="">
            <!--立即登录-->
            <span class="f17">去投资</span>
        </a>
    </div>
    <!--card-box-->
    <div class="card-box">
        <img class="line" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/line.png" alt="">
        <img class="close-card" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/close.png" alt="">
        <img class="card-img" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/card.png" alt="">
        <div class="card-title"></div>
        <div class="card-content f10">我们会在7个工作日内与您联系并发放奖励</div>
        <a href="javascript:;" class="card-btn"><img src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/btn.png" alt="">
            <!--立即登录-->
            <span class="f17">查看奖品</span>
        </a>
    </div>
    <!--获奖记录-->
    <div class="myprize-box">
        <img class="close-prize" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/close.png" alt="">
        <div class="prize-part1"></div>
        <div id="wrapper">
            <ul class="prize-part2">
                <?php foreach ($userDraws as $userDraw) { ?>
                    <li>
                        <div class="pic lf" style="background: url('<?= FE_BASE_URI.$userDraw->reward->path ?>') center center no-repeat;background-size: contain;"></div>
                        <p class="prize-name"><?= $userDraw->reward->name ?></p>
                        <p class="prize-time"><?= date('Y-m-d H:i:s', $userDraw->drawAt) ?></p>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="prize-part3"></div>
    </div>
    <!--无中奖记录-->
    <div class="nogift-box">
        <img class="close-nogift" src="<?= FE_BASE_URI ?>wap/campaigns/golden-egg/images/close.png" alt="">
        <p class="f17">您还未获得任何奖品！</p>
    </div>
</div>

<script>
    $(function () {
        forceReload_V2();

        var cdn = '<?= FE_BASE_URI ?>';

        var ei = 0;
        var et = setInterval(function () {
            $('.banner-egg').attr({src: cdn+'wap/campaigns/golden-egg/images/egg.png'});
            $('.banner-egg').eq(ei).attr({src: cdn+'wap/campaigns/golden-egg/images/eggs.png'});
            ei++;
            if (ei > 2) {
                ei = 0;
            }
        }, 1000);

        //点击我的奖品按钮
        $('.my_prize').on('click', function () {
            $("body,html").css({"overflow":"hidden"});
            if ($('#wrapper ul').find('li').length) {
                //有奖品时，显示奖品列表
                $('.myprize-box').fadeIn().addClass('myprize-box1');
            } else {
                //无奖品时，显示弹窗提示
                $('.nogift-box').fadeIn().addClass('nogift-box1');
            }

            $('.mark-box').fadeIn();
            $('body').on('touchmove', eventTarget, false);
        });

        $(window).load(function () {
            $('.banner-egg').bind('click', function () {
                $('.banner-egg').unbind('click');
                clearInterval(et);
                var index = $('.banner-egg').index(this);
                $('.banner-egg').attr({src: cdn+'wap/campaigns/golden-egg/images/egg.png'});
                for (var i = 0; i <= $('.banner-egg').length; i++) {
                    var bE = $('.banner-egg').eq(i).attr('data-n');
                    if (bE == 'true') {
                        $('.banner-egg').eq(i).attr({src: cdn+'wap/campaigns/golden-egg/images/egg1.png'});
                    }
                }
                $('.banner-chui').eq(index).show();
                $('.banner-chui').eq(index).css({
                    '-webkit-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
                    '-o-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
                    '-moz-animation': 'yumove 0.5s cubic-bezier(0.42, 0, 0.59, 0.97)',
                });
                setTimeout(function () {
                    $('.banner-lie0').eq(index).fadeIn();
                    eggClick(index);
                }, 600);
            });

            function eggClick(index) {
                var s = 1;
                var t = window.setInterval(function () {
                    $('.banner-lie0').eq(index).attr('src', cdn+'wap/campaigns/golden-egg/images/liewen' + s + '.png');
                    s++;
                    if (s == 5) {
                        $('.banner-chui').eq(index).hide();
                        window.clearInterval(t);
                        $('.banner-lie0').hide();
                        $('.banner-egg').eq(index)[0].src = cdn+'wap/campaigns/golden-egg/images/egg1.png?v=1.1';
                        $('.banner-egg').eq(index).attr({'data-n': 'true'});
                        $('.banner-hua').eq(index).fadeIn();
                        $.ajax({
                            url: '/promotion/smashegg/draw',
                            type: "get",
                            dataType: "json",
                            success: function (data) {
                                // 温都金服逻辑:   data.code: 101  未登录
                                //                          102   没有砸蛋机会
                                //                          200   中奖了
                                //                          data.type == POINT 积分
                                //                          data.type == PIKU  实物
                                //                          400   程序出错

                                //请按照上方逻辑,调整温都金服显示
                                if (data.code == 101) { //登录后再砸吧
                                    $('.login-inner').html(data.msg);
                                    $('.login-img img').attr({src: cdn+'wap/campaigns/golden-egg/images/nologin.png'});
                                    $('.mark-box').fadeIn();
                                    $('.login-box').show();
                                    setTimeout(function () {
                                        $('.login-box').addClass('login-box1');
                                    }, 100);
                                    $('.login-btn span').html('立即登录');
                                    $('.login-btn').attr({href: '<?= $loginUrl ?>'});
                                } else if (data.code == 102) { //没有砸蛋机会,去投资(理财列表)
                                    $('.login-inner').html(data.msg);
                                    $('.login-img img').attr({src: cdn+'wap/campaigns/golden-egg/images/noChange.png'});
                                    $('.mark-box').fadeIn();
                                    $('.login-box').show();
                                    setTimeout(function () {
                                        $('.login-box').addClass('login-box1');
                                    }, 100);
                                    $('.login-btn span').html('去投资');
                                    $('.login-btn').attr({href: '/deal/deal/index'});
                                } else if (data.code == 200) {
                                    $('.card-title').html('获得'+data.name);
                                    $('.mark-box').fadeIn();
                                    $('.card-box').show();
                                    setTimeout(function () {
                                        $('.card-box').addClass('card-box1');
                                    }, 100);
                                    if ('POINT' == data.type) { //获取积分
                                        $('.card-btn span').html('查看奖品');
                                        $('.card-title').css({top: '46%'});
                                        $('.card-content').hide();
                                        $('.card-btn').attr({href: '/mall/point/list'});
                                    } else { //获取实物
                                        $('.card-btn span').html('确认');
                                        $('.card-btn span').on('click', function () {
                                            $('.card-box').removeClass('card-box1');
                                            setTimeout(function () {
                                                $('.card-box').hide();
                                                refreshPage();
                                            }, 500);
                                            $('.mark-box').fadeOut();
                                        })
                                    }
                                } else if (data.code == 400) {
                                    alert(data.msg);
                                }
                            }
                        });
                    }
                }, 200);
            }
        })
    })
</script>