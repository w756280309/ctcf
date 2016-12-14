<?php

$this->title = '温都金服';
$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$this->showBottomNav = true;

use common\utils\StringUtils;
use common\view\LoanHelper;
use \yii\helpers\Html;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/base.css?v=2.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/index/css/index.css?v=2.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/swiper/swiper.min.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible.js"></script>
<header>
    <img src="<?= FE_BASE_URI ?>wap/index/images/logo.png" alt="">
    <div class="notLogin hide">
        <p>注册就送<span>288元</span>专享红包</p>
        <a href="/site/signup">注册</a>
        <a href="/site/login">登录</a>
    </div>

    <div class="login hide">
        <p>优质项目，收益稳健</p>
        <p><span>5.5%-9%</span>预期年化率</p>
        <a href="/deal/deal/index">去投资</a>
    </div>

    <div class="loginNewPeople hide">
        <p>新手专享标，超短期</p>
        <p><span>10%</span>预期年化率</p>
        <a href="/deal/deal/index">去投资</a>
    </div>

</header>
<section>
    <div class="introduce"><a href="/site/h5?wx_share_key=h5"><img src="<?= FE_BASE_URI ?>wap/index/images/introduce.png" alt=""></a></div>
    <div class="featured">
        <p class="project hide">精选项目</p>
        <!--两张图片-->
        <ul class="clearfix twopic">
            <li class="lf">
                <a href="/deal/deal/index"><img src="<?= FE_BASE_URI ?>wap/index/images/bgproject_01.png" alt=""></a>
            </li>
            <li class="rg">
                <a href="/deal/deal/index"><img src="<?= FE_BASE_URI ?>wap/index/images/bgproject_04.png" alt=""></a>
            </li>
        </ul>
    </div>
</section>

<?php if (!empty($loans)) { ?>
<div class="invest">
    <p class="clearfix">
        <span class="lf">理财专区</span>
        <a class="rg" href="/deal/deal/index">查看全部产品</a>
    </p>
    <ul class="clearfix">
        <?php foreach ($loans as $loan) { ?>
        <li class="lf commonLi">
            <a <?php if ($loan->is_xs) { ?>class="superscript"<?php } ?> href="/deal/deal/detail?sn=<?= $loan->sn ?>">
                <div><span><?= $loan->title ?></span></div>
                <div>
                    <?= LoanHelper::getDealRate($loan) ?>%<?php if (!empty($loan->jiaxi)) { ?><span>+<?= StringUtils::amountFormat2($loan->jiaxi) ?>%</span><?php } ?>
                </div>
                <div>预期年化率</div>
                <?php $ex = $loan->getDuration() ?>
                <div>期限&nbsp;&nbsp;<?= $ex['value'].$ex['unit'] ?></div>
                <?php
                    if (null !== $loan->tags) {
                        $tags = explode('，', $loan->tags);
                ?>
                <div>
                    <?php
                        foreach($tags as $key => $tag) {
                            if ($key < 2) {
                    ?>
                        <span><?= Html::encode(mb_substr($tag, 0, mb_strlen($tag) < 4 ? mb_strlen($tag) : 4, 'UTF-8')) ?></span>
                    <?php
                            }
                        }
                    ?>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<?php if (!empty($hotActs)) : ?>
<div class="activity">
    <p>热门活动</p>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach($hotActs as $act) : ?>
                <div class="swiper-slide"><a href="<?= $act->getLinkUrl() ?>"><img src="<?= UPLOAD_BASE_URI ?>upload/adv/<?= $act->image ?>" alt=""></a></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($news)) : ?>
<div class="notices">
        <ul>
            <li><span class="lf">公告专区</span><a href="/news/index" class="rg">更多</a></li>
            <?php foreach ($news as $key => $new) : ?>
                <li class="list"><a href="/news/detail?id=<?= $new->id ?>"><?= $new->title ?></a></li>
            <?php endforeach; ?>
        </ul>
</div>
<?php endif; ?>

<div class="shareholder">
    <p>股东背景</p>
    <ul>
        <li></li>
        <li><img src="<?= FE_BASE_URI ?>wap/index/images/shareholde_01.png" alt=""><img src="<?= FE_BASE_URI ?>wap/index/images/shareholde_02.png" alt=""></li>
    </ul>
</div>

<div class="platform">
    <p>平台优势</p>
    <ul class="clearfix">
        <li></li>
        <li class="lf platformList">
            <div class="platformListFirst">
                <img src="<?= FE_BASE_URI ?>wap/index/images/platform_01.png" alt="">
                <div>国资背景</div>
                <div>温州报业传媒旗下</div>
            </div>
        </li>
        <li class="lf platformList">
            <div class="platformListSecond">
                <img src="<?= FE_BASE_URI ?>wap/index/images/platform_02.png" style="width: 1.1rem;" alt="">
                <div>资金稳妥</div>
                <div>第三方资金托管</div>
            </div>
        </li>
        <li class="lf platformList">
            <div>
                <img src="<?= FE_BASE_URI ?>wap/index/images/platform_03.png" alt="">
                <div>收益稳健</div>
                <div>预期收益5.5~9%</div>
            </div>
        </li>
        <li class="lf platformList">
            <div class="platformListLast">
                <img src="<?= FE_BASE_URI ?>wap/index/images/platform_04.png" alt="">
                <div>产品优质</div>
                <div>国企、政信类产品</div>
            </div>
        </li>
    </ul>
</div>

<div class="address">
    <a href="tel:<?= Yii::$app->params['contact_tel'] ?>"><img src="<?= FE_BASE_URI ?>wap/index/images/phone.png" alt=""><?= Yii::$app->params['contact_tel'] ?></a>
    <p>温州市鹿城区飞霞南路657号保丰大楼四层</p>
</div>

<footer><span></span>温州报业传媒旗下理财平台<span></span></footer>

<div class="mask" style="display: none;">
</div>

<div class="pop" style="display: none;">
    <img src="<?= FE_BASE_URI ?>wap/index/images/close.png" class="close_splash" alt="">
    <img src="<?= FE_BASE_URI ?>wap/index/images/kaiping.png" class="jumpAdv" alt="">
</div>
<script src="<?= ASSETS_BASE_URI ?>js/swiper.min.js"></script>
<script>
    var mySwiper = new Swiper('.swiper-container', {
        loop:true,
//        loopedSlides:5,
        slidesPerView: 2.4,//可选选项，自动滑动
        spaceBetween:20,
        slidesOffsetBefore:20,
//        slidesOffsetAfter:10,
    });
    function closeAdv()
    {
        $('.mask').hide();
        $('.pop').hide();
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
            if (code >= 0) {
                if (code) {
                    $('.login').removeClass('hide').siblings('div').addClass('hide');
                } else {
                    $('.loginNewPeople').removeClass('hide').siblings('div').addClass('hide');
                }
            } else {
                $('.notLogin').removeClass('hide').siblings('div').addClass('hide');
            }
        });
    }

    $(function () {
        $('.close_splash').on('click', closeAdv);
        if ($.cookie('splash_show') !== "20161214") {
            document.body.addEventListener('touchmove', eventTarget);
            $.cookie('splash_show', "20161214");
            $('.mask').show();
            $('.pop').show();
            setTimeout(closeAdv, 4000);
        }
        //判断首页上方
        checkStatus();

        $('.jumpAdv').on('click', function () {
            location.href = '/promotion/p1612/double-twelves?wx_share_key=14813409247';
        });
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('footer').css({'margin-bottom':0});
        }
    });
</script>
</body>
</html>