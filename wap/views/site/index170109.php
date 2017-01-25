<?php

$this->title = '温都金服';
$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;

use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\Html;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/base.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/comfont.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/index/css/index.css?v=20170119">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/swiper/swiper.min.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/swiper.min.js"></script>

<div class="container flex-content">
    <?php if (!empty($hotActs)) { ?>
        <div class="banner swiper-container">
            <div class="swiper-wrapper">
            <?php foreach($hotActs as $act) { ?>
                <a class="swiper-slide" href="<?= $act->getLinkUrl() ?>">
                    <img src="<?= UPLOAD_BASE_URI ?>upload/adv/<?= $act->image ?>" alt="">
                </a>
            <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    <?php } ?>

    <div class="login clearfix hide" id="login">
        <a class="f15 lf" href="/site/login">登录</a>
        <a class="f15 rg" href="/site/signup">注册</a>
    </div>

    <div class="showMore">
        <p class="clearfix">
            <span class="f12 lf" id="totalTradeAmount">交易额<i></i>万元</span>
            <span class="f12 rg">历史兑付率100%</span>
        </p>
        <div class="clearfix">
            <a class="f13 lf" href="/site/h5?wx_share_key=h5">
                <img src="<?= FE_BASE_URI ?>wap/index/images/icon_01.png" alt="">
                <p>平台介绍</p>
            </a>
            <a class="f13 lf" href="/user/invite">
                <img src="<?= FE_BASE_URI ?>wap/index/images/icon_02.png" alt="">
                <p>邀请好友</p>
            </a>
            <a class="f13 lf" href="/mall/portal/guest<?= (Yii::$app->request->get('token') && defined('IN_APP')) ? '?token='.Yii::$app->request->get('token') : ''?>">
                <img src="<?= FE_BASE_URI ?>wap/index/images/icon_03.png" alt="">
                <p>积分商城</p>
            </a>
            <a class="f13 lf" href="/news">
                <img src="<?= FE_BASE_URI ?>wap/index/images/icon_04.png" alt="">
                <p>官网公告</p>
            </a>
        </div>
    </div>

    <?php if ($xs) { ?>
        <div class="newnorm hide" id="loginNewPeople">
            <p class="newnormtitle f15">新手专享
            <?php
                if (null !== $xs->tags) {
                    $tags = explode('，', $xs->tags);
                    foreach($tags as $key => $tag) {
                        if ($key < 2 && !empty($tag)) {
                            echo '<span class="f12">'.Html::encode($tag).'</span>';
                        }
                    }
                }
            ?>
            </p>
            <?php $ex = $xs->getDuration() ?>
            <?php if (!$xs->isFlexRate && !$xs->jiaxi) { ?>
                <a href="/deal/deal/index">
                    <p class="f15"><?= $xs->title ?></p>
                    <p class="f45"><?= LoanHelper::getDealRate($xs) ?><i class="f25">%</i></p>
                    <p class="f12">预期年化率</p>
                    <ul class="clearfix">
                        <li class="f14 lf text-align-lf"><img class="icon5" src="<?= FE_BASE_URI ?>wap/index/images/icon_05.png" alt=""> <?= StringUtils::amountFormat2($xs->start_money) ?>元起投</li>
                        <li class="f14 lf comred"><img class="icon6" src="<?= FE_BASE_URI ?>wap/index/images/icon_06.png" alt=""> <?= $ex['value'].$ex['unit'] ?>期限</li>
                        <li class="f14 lf text-align-rg"><img class="icon7" src="<?= FE_BASE_URI ?>wap/index/images/icon_07.png" alt=""> 限购一万</li>
                        <li class="f18 lf">去理财</li>
                    </ul>
                </a>
            <?php } else { ?>
                <!--加息-->
                <a href="/deal/deal/index">
                    <p class="f15"><?= $xs->title ?></p>
                    <p class="f30"><em><?= LoanHelper::getDealRate($xs) ?><i class="f20">%</i><?php if (!empty($xs->jiaxi)) { ?><span class="f15">+<?= StringUtils::amountFormat2($xs->jiaxi) ?>%</span><?php } ?></em></p>
                    <p class="f12">预期年化率</p>
                    <ul class="clearfix">
                        <li class="f14 lf text-align-lf"><img class="icon5" src="<?= FE_BASE_URI ?>wap/index/images/icon_05.png" alt=""> <?= StringUtils::amountFormat2($xs->start_money) ?>元起投</li>
                        <li class="f14 lf comred"><img class="icon6" src="<?= FE_BASE_URI ?>wap/index/images/icon_06.png" alt=""> <?= $ex['value'].$ex['unit'] ?>期限</li>
                        <li class="f14 lf text-align-rg"><img class="icon7" src="<?= FE_BASE_URI ?>wap/index/images/icon_07.png" alt=""> 限购一万</li>
                        <li class="f18 lf">去理财</li>
                    </ul>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <section>
        <div class="featured">
            <p class="project f15">精选项目</p>
            <!--一张图片-->
            <!--<ul class="clearfix onepic">-->
            <!--<li>-->
            <!--<a href=""><img src="images/projectone.png" alt=""></a>-->
            <!--</li>-->
            <!--</ul>-->

            <!--两张图片-->
            <ul class="clearfix twopic">
                <li class="lf">
		            <a href="/issuer?id=5&type=2"><img src="<?= FE_BASE_URI ?>wap/index/images/bgproject_05.png" alt=""></a>
                </li>
                <li class="rg">
                    <a href="/issuer?id=2&type=1"><img src="<?= FE_BASE_URI ?>wap/index/images/sandu_new.png" alt=""></a>
                </li>
            </ul>

            <!--三张图片-->
            <!--<ul class="clearfix threepic">-->
            <!--<li class="lf">-->
            <!--<a href=""><img src="images/bgproject_01.png" alt=""></a>-->
            <!--</li>-->
            <!--<li class="rg">-->
            <!--<a href=""><img src="images/bgproject_02.png" alt=""></a>-->
            <!--<span class="a-two"></span>-->
            <!--<a href=""><img src="images/bgproject_03.png" alt=""></a>-->
            <!--</li>-->
            <!--</ul>-->
        </div>
    </section>

    <?php if (!empty($loans)) { ?>
        <div class="invest">
            <p class="clearfix">
                <span class="lf f15">理财专区</span>
                <a class="rg f15" href="/deal/deal/index">查看全部产品</a>
            </p>
            <ul>
            <?php foreach ($loans as $loan) { ?>
                <?php $ex = $loan->getDuration() ?>
                <li>
                    <a class="superscript" href="/deal/deal/detail?sn=<?= $loan->sn ?>">
                        <div class="f14 supertitle"><?= $loan->title ?></div>
                        <div class="superctn clearfix">
                            <div class="lf">
                                <p><em class="f24"><?= LoanHelper::getDealRate($loan) ?><span class="f15">%</span><?php if (!empty($loan->jiaxi)) { ?><i class="f12">+<?= StringUtils::amountFormat2($loan->jiaxi) ?>%</i><?php } ?></em></p>
                                <p class="f14">预期年化率</p>
                            </div>
                            <div class="lf">
                                <p class="f14 months"><span class="f16"><?= $ex['value'] ?></span><?= $ex['unit'] ?></p>
                                <p class="f14 money"><span class="f16"><?= StringUtils::amountFormat2($loan->start_money) ?></span>元起投</p>
                            </div>
                        </div>
                        <div class="progessline clearfix">
                            <div class="progess lf">
                                <span style="width: <?= $loan->getProgressForDisplay() ?>%;"></span>
                            </div>
                            <i class="rg f14"><?= $loan->getProgressForDisplay() ?>%</i>
                        </div>
                        <div class="tags clearfix">
                            <?php if (null !== $loan->tags) { ?>
                            <p class="lf f12">
                            <?php
                                $tags = explode('，', $loan->tags);
                                foreach($tags as $key => $tag) {
                                    if ($key < 2 && !empty($tag)) {
                                        echo '<span>'.Html::encode($tag).'</span>';
                                    }
                                }
                            ?>
                            </p>
                            <?php } ?>
                            <p class="rg f14"><?= Yii::$app->params['refund_method'][$loan->refund_method] ?></p>
                        </div>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if (!empty($news)) { ?>
        <div class="notices">
            <ul>
                <li><span class="lf f15">公告专区</span><a href="/news" class="rg f12">更多</a></li>
                <?php foreach ($news as $key => $new) { ?>
                    <li class="list"><a class="f14" href="/news/detail?id=<?= $new->id ?>&v=<?= time() ?>"><?= $new->title ?></a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <div class="shareholder">
        <p class="f15">股东背景</p>
        <ul>
            <li></li>
            <li><img src="<?= FE_BASE_URI ?>wap/index/images/shareholde_01.png" alt=""><img src="<?= FE_BASE_URI ?>wap/index/images/shareholde_02.png" alt=""></li>
        </ul>
    </div>

    <div class="platform">
        <p class="f15">平台优势</p>
        <ul class="clearfix">
            <li></li>
            <li class="lf platformList">
                <div class="platformListFirst">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/platform_01.png" alt="">
                    <div class="f15">国资背景</div>
                    <div class="f14">温州报业传媒旗下</div>
                </div>
            </li>
            <li class="lf platformList">
                <div class="platformListSecond">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/platform_02.png" style="width: 1.1rem;" alt="">
                    <div class="f15">资金稳妥</div>
                    <div class="f14">第三方资金托管</div>
                </div>
            </li>
            <li class="lf platformList">
                <div>
                    <img src="<?= FE_BASE_URI ?>wap/index/images/platform_03.png" alt="">
                    <div class="f15">收益稳健</div>
                    <div class="f14">预期收益5.5~9%</div>
                </div>
            </li>
            <li class="lf platformList">
                <div class="platformListLast">
                    <img src="<?= FE_BASE_URI ?>wap/index/images/platform_04.png" alt="">
                    <div class="f15">产品优质</div>
                    <div class="f14">国企、政信类产品</div>
                </div>
            </li>
        </ul>
    </div>

    <div class="total">
        <ul class="f13 clearfix">
            <li class="lf" id="totalRefundAmount">累计兑付<span></span>万元</li>
            <li class="rg" id="totalRefundInterest">带来<span></span>万元收益</li>
        </ul>
    </div>

    <div class="address">
        <a class="f14" href="tel:400-101-5151"><img src="<?= FE_BASE_URI ?>wap/index/images/phone.png" alt="">400-101-5151</a>
        <p class="f14">温州市鹿城区飞霞南路657号保丰大楼四层</p>
    </div>

    <footer class="f11"><span></span>温州报业传媒旗下理财平台<span></span></footer>

    <?php if (!defined('IN_APP')) { ?>
        <div class="navbar-fixed-bottom nav-footer">
            <div class="nav-footer-title">
                <div class="nav-footer-inner">
                    <a href="/#t=1" class="f14"><img src="<?= FE_BASE_URI ?>wap/index/images/nav-footer_01.png" alt=""><p class="pitched">首页</p></a>
                </div>
            </div>
            <div class="nav-footer-title">
                <div class="nav-footer-inner1">
                    <a href="/deal/deal/index" class="f14"><img src="<?= FE_BASE_URI ?>wap/index/images/nav-footer_02.png" alt=""><p>理财</p></a>
                </div>
            </div>
            <div class="nav-footer-title">
                <div class="nav-footer-inner2">
                    <a href="<?= Yii::$app->user->isGuest ? '/site/login' : '/user/user' ?>"  class="f14"><img src="<?= FE_BASE_URI ?>wap/index/images/nav-footer_03.png" alt=""><p>账户</p></a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div class="mask hide">
</div>

<div class="pop hide">
    <img src="<?= FE_BASE_URI ?>wap/index/images/close.png" class="close_splash" alt="">
    <img src="<?= ASSETS_BASE_URI ?>images/index/kaipin/170109.jpg" class="jumpAdv" alt="">
</div>

<script>
    var mySwiper = new Swiper('.swiper-container', {
        autoplay: 4000,
        autoplayDisableOnInteraction: false,
        loop: true,
        pagination: '.swiper-pagination',
    });

    function closeAdv()
    {
        $('.mask').addClass('hide');
        $('.pop').addClass('hide');
        document.body.removeEventListener('touchmove', eventTarget, false);
    }

    function eventTarget(event)
    {
        event.preventDefault();
    }

    function checkStatus()
    {
        var xhr = $.get('/site/xs');
        xhr.done(function(code) {
            if (code === -1) {
                $('#login').removeClass('hide');
                $('#loginNewPeople').removeClass('hide');
            } else if (code === 0) {
                $('#loginNewPeople').removeClass('hide');
            }
        });
    }

    $(function () {
        FastClick.attach(document.body);

        //开屏图逻辑
        /*
        $('.close_splash').on('click', closeAdv);
        if (Cookies.get('splash_show') !== '20170109') {
            document.body.addEventListener('touchmove', eventTarget);
            Cookies.set('splash_show', '20170109');
            $('.mask').removeClass('hide');
            $('.pop').removeClass('hide');
            setTimeout(closeAdv, 6000);
        }

        $('.jumpAdv').on('click', function () {
            location.href = '/deal/deal/index';
        });
        */

        //统计数据
        $.get('/site/stats-for-index', function (data) {
            $('#totalTradeAmount i').html(WDJF.numberFormat(accDiv(data.totalTradeAmount, 10000), 0));
            $('#totalRefundAmount span').html(WDJF.numberFormat(accDiv(data.totalRefundAmount, 10000), 0));
            $('#totalRefundInterest span').html(WDJF.numberFormat(accDiv(data.totalRefundInterest, 10000), 0));
        });

        //判断首页上方
        checkStatus();

        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('footer').css({'margin-bottom': 0});
        }
    });
</script>
