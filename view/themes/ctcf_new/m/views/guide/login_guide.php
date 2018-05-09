<?php
$this->showBottomNav = true;
$this->title = '登录/注册';
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => \yii\web\JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/swiper.min.js', ['depends' => \yii\web\JqueryAsset::class, 'position' => 1]);
$this->registerJs('forceReload_V2();');
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/swiper/swiper.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<style>
    .logo{width:100%;height:3.93333333rem;padding:1.16666667rem 0;background-color:#fff}.logo img{width:76.66666667%;display:block;margin:0 auto}.section-one{background:-webkit-linear-gradient(to bottom,#ff554d 0,#f3473f 100%);background:-moz-linear-gradient(to bottom,#ff554d 0,#f3473f 100%);background:-ms-linear-gradient(to bottom,#ff554d 0,#f3473f 100%);background:-o-linear-gradient(to bottom,#ff554d 0,#f3473f 100%);background:linear-gradient(to bottom,#ff554d 0,#f3473f 100%);background-image:-moz-linear-gradient(top,#ff554d,#f3473f);background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,#ff554d),color-stop(1,#f3473f))}.section-one .banner{width:100%;height:5.6rem;padding:1.06666667rem 0}.section-one .banner img{width:100%;height:100%}.section-one .feature{padding:0 9.33333333% .46666667rem}.section-one .feature p.tips{line-height:1;margin:.34666667rem 0;color:#fff;font-size:.32rem}.section-one .feature a{width:100%;height:1.33333333rem;line-height:1.33333333rem;font-size:.45333333rem;-webkit-border-radius:.13333333rem;-moz-border-radius:.13333333rem;border-radius:.13333333rem;text-align:center;display:block}.section-one .feature a.login{color:#f1453d;background-color:#fff}.section-one .feature a.register{color:#fff;margin:.49333333rem 0 1rem;border:1px solid #fff}.section-one .feature .more{height:1rem}.section-one .feature .more p{text-align:center;margin-bottom:.26666667rem;line-height:1;color:#f8b39e;font-size:.32rem}.section-one .feature .more img{position:relative;top:0;left:0;display:block;width:8.8%;margin:0 auto;animation:mymove 1s infinite both;-webkit-animation:mymove 1s infinite both;-moz-animation:mymove 1s infinite both;-o-animation:mymove 1s infinite both}@-webkit-keyframes mymove{0%{top:0}50%{top:.16rem}100%{top:0}}@keyframes mymove{0%{top:0}50%{top:.16rem}100%{top:0}}.section-two{padding:.53333333rem 5.33333333% 0;background-color:#fff;overflow:hidden}.section-two .compare img{width:100%;display:block;margin:.66666667rem auto .8rem}.section-two .compare p{text-align:center;font-size:.4rem;color:#333;line-height:.64rem}.section-two .compare p span{color:#f1453d}.section-two .compare ul{margin:.13333333rem 6.66666667% 0;border-top:1px solid #ccc}.section-two .compare ul li{font-size:.37333333rem;color:#999;line-height:.66666667rem}.section-two .compare ul li span{color:#000}.section-three{background-color:#f1673d;padding:1.28rem 5.33333333% 1.01333333rem}.section-three .introduce .intro-header{position:relative;top:0;left:0}.section-three .introduce .intro-header .intro-bg{width:100%;height:1.08rem;display:block}.section-three .introduce .intro-header .intro-title{color:#fff;font-size:.4rem;line-height:1rem;text-align:center;position:absolute;width:100%;top:0;left:0}.section-three .introduce .intro-ctn{width:100%;padding:0 .53333333rem;background-color:#fff;border-bottom-left-radius:.26666667rem;border-bottom-right-radius:.26666667rem}.section-three .introduce .intro-ctn div{overflow:hidden}.section-three .introduce .intro-ctn div p.des{font-size:.4rem;color:#333;margin:.48rem 0;line-height:1;font-weight:500}.section-three .introduce .intro-ctn div p.detail{font-size:.37333333rem;margin-bottom:.46666667rem;line-height:.64rem;color:#666}.section-three .introduce .intro-ctn div .media-img{width:100%;display:block;margin-bottom:.46666667rem}.section-three .introduce .intro-ctn div .intro-img{width:100%;display:block;margin:.2rem 0 .66666667rem}.section-three .intro-footer{margin-top:.64rem}.section-three .intro-footer ul{width:66.26865672%;color:#fff;padding-top:.35rem}.section-three .intro-footer ul li{width:100%;white-space:nowrap;overflow:hidden}.section-three .intro-footer ul li:first-child{font-size:.46666667rem}.section-three .intro-footer ul li:first-child span{font-size:.32rem}.section-three .intro-footer ul li:nth-of-type(2){font-size:.37333333rem}.section-three .intro-footer ul li:nth-of-type(3){font-size:.32rem;color:#f8b39e}.section-three .intro-footer img.erweima{width:33.73134328%;display:block}.swiper-pagination-bullet-active{background-color:#fff}.toTop{position:fixed;right:0;bottom:3.53333333rem;width:1.33333333rem;display:none}
</style>
<div class="flex-content">
    <div class="logo"><img src="<?= FE_BASE_URI ?>wap/guide/images/logo.png" alt=""></div>

    <div class="section-one">
        <div class="banner swiper-container">
            <div class="swiper-wrapper">
                <img class="swiper-slide" src="<?= FE_BASE_URI ?>wap/guide/images/banner.png" alt="">
                <img class="swiper-slide" src="<?= FE_BASE_URI ?>wap/guide/images/banner_01.png" alt="">
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="feature">
            <p class="tips">登录获得更多个性体验</p>
            <a class="login" href="/site/login">登录</a>
            <a class="register" href="/site/signup">注册</a>
            <div class="more">
                <p>了解更多</p>
                <img src="<?= FE_BASE_URI ?>wap/guide/images/pointer.png" alt="">
            </div>
        </div>
    </div>

    <div class="section-two">
        <div class="compare">
            <p>楚天财富平台已安全运营<span><?= (new \DateTime(date('Y-m-d')))->diff(new DateTime('2016-05-20'))->days ?>天
            <div>
                <img src="<?= FE_BASE_URI ?>wap/guide/images/compare.png" alt="">
            </div>
        </div>
    </div>

    <div class="section-three">
        <div class="introduce">
            <div class="intro-header">
                <img class="intro-bg" src="<?= FE_BASE_URI ?>wap/guide/images/intro_bg.png" alt="">
            </div>
            <div class="intro-ctn">
                <div>
                    <p class="des">什么是楚天财富？</p>
                    <p class="detail">楚天财富是隶属湖北日报新媒体集团旗下的金融平台。甄选各类权威机构的产品。提供银行级出借服务，保障用户资金安全，安享稳定收益。</p>
                </div>

                <div>
                    <p class="des">股东背景</p>
                    <img class="media-img" src="<?= FE_BASE_URI ?>wap/guide/images/media_new.png" alt="">
                </div>

                <div>
                    <p class="des">平台优势</p>
                    <img class="intro-img" src="<?= FE_BASE_URI ?>wap/guide/images/intro.png" alt="">
                </div>
            </div>

            <div class="intro-footer clearfix">
                <ul class="lf">
                    <li><?= Yii::$app->params['platform_info.contact_tel'] ?><span>（9:00～20:00）</span></li>
                    <li>地址：武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层</li>
                    <li>*产品有风险 出借须谨慎</li>
                </ul>
                <img class="erweima" src="<?= FE_BASE_URI ?>wap/guide/images/erweima.png" alt="">
            </div>
        </div>
    </div>
    <img class="toTop" src="<?= FE_BASE_URI ?>wap/guide/images/toTop.png" alt="">
</div>
<?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
    <div style="height: 50px;"></div>
    <?= $this->renderFile('@wap/views/layouts/footer.php')?>
<?php } ?>

<script>
    //轮播图
    var mySwiper = new Swiper('.swiper-container', {
//            autoplay: 4000,
        autoplayDisableOnInteraction: false,
        loop: true,
        pagination: '.swiper-pagination'
    });

    $(window).on("scroll",function(){
        if($(this).scrollTop()>400){
            $(".toTop").show();
        } else {
            $(".toTop").hide();
        }
    });
    $(".toTop").on('click',function(){
        $("body,html").animate({scrollTop: 0},500);
    });
    $(".more").on('click',function(){
        var top = $(".section-two").offset().top;
        $("body,html").animate({scrollTop: top},500);
    })

</script>
