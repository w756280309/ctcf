<?php

$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
if ($fromNb) {
    $this->title = '现代金报-温都金服';
} else {
    $this->title = '温都金服';
}

$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$this->showBottomNav = true;

use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->registerCssFile(FE_BASE_URI . "libs/swiper/swiper-3.4.2.min.css");
$this->registerCssFile(FE_BASE_URI . "wap/common/css/wenjfbase.css?v=171028");
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'res/js/js.cookie.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'libs/fastclick.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'libs/jquery.lazyload.min.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'libs/swiper/swiper-3.4.2.min.js', ['depends' => JqueryAsset::class]);
?>
<style>
    .pop{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);z-index:100000}.pop img:nth-of-type(1){width:.76rem;position:absolute;top:-11.5%;right:0}.pop img:nth-of-type(2){width:8rem;height:10.66667rem}
</style>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/new-homepage/css/index.css?v=171028">
<div class="flex-content">
    <?php if (!empty($hotActs)) { ?>
    <div class="banner-box swiper-container">
        <div class="swiper-wrapper" id="index_banner">
            <?php
                $firstHotAdv = current($hotActs);
                if (isset($firstHotAdv) && !is_null($firstHotAdv->media)) {
            ?>
            <a href="<?= $firstHotAdv->getLinkUrl() ?>" class="swiper-slide"><img src="<?= UPLOAD_BASE_URI . $firstHotAdv->media->uri ?>" alt=""></a>
             <?php
                }
             ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <?php } ?>
    <div class="newbid-box" style="display:none">
        <div class="newbid-insideBox">
            <span class="newbid-insideBox-title">新手专享</span>
            <p class="newbid-insideBox-shouyiNum">10%</p>
            <p class="newbid-insideBox-shouyiTxt">预期年化收益</p>
            <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/pic_newbid.png" class="newbid-insideBox-pic">
            <a href="/site/signup" class="newbid-insideBox-register">注册</a>
            <a href="/site/login" class="newbid-insideBox-login">登录</a>
        </div>
        <p class="newbid-wendu-link"><a href="/site/h5?wx_share_key=h5">1分钟了解温都金服</a></p>
        <p class="newbid-wendu-txt">温州报业传媒旗下理财平台</p>
    </div>
    <div class="links-box clearfix">
        <a href="/user/checkin" class="lf">
            <span class="links-box-iconJifen"></span>
            <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/icon_qd.png" alt="" style="width: 0.693rem;margin-left: -0.35rem;">
            <p class="links-box-txt">签到</p>
        </a>
        <a href="/site/app-download?redirect=/mall/portal/guest<?= (Yii::$app->request->get('token') && defined('IN_APP')) ? '?token='.Yii::$app->request->get('token') : ''?>" class="lf">
            <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/icon_jf.png" alt="" style="width: 0.6rem;margin-left: -0.3rem;">
            <p class="links-box-txt">积分商城</p>
        </a>
        <a href="/user/invite" class="lf">
            <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/icon_yq.png" alt="" style="width: 0.56rem;margin-left: -0.28rem;">
            <p class="links-box-txt">邀请好友</p>
        </a>
        <a href="/news" class="lf">
            <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/icon_gg.png" alt="" style="width: 0.6266rem;margin-left: -0.3133rem;">
            <p class="links-box-txt">公告</p>
        </a>
    </div>
    <div class="bids-box">
        <p class="bids-box-title"><span class="lf">理财专区</span><a href="/deal/deal/index" class="rg">更多 ></a></p>
        <ul class="bids-box-bidlist clearfix">
            <li>
                <a href="/deal/deal/index">
                <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/pic_bidList_1.png" alt="">
                </a>
            </li>
            <li>
                <a href="/deal/deal/index">
                <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/pic_bidList_2.png" alt="">
                </a>
            </li>
        </ul>
    </div>
    <?php if (!empty($news)) { ?>
    <div class="reading-box">
        <p class="reading-box-title">理财阅读</p>
        <ul class="reading-box-list">
            <?php foreach ($news as $key => $new) { ?>
            <li>
                <a href="/news/detail?id=<?= $new->id ?>&v=<?= time() ?>">
                    <p class="reading-box-list-til"><?= $new->title ?></p>
                    <p class="reading-box-list-des clearfix">
                        <span class="lf">网站公告</span>
                        <span class="rg"><?= date('Y-m-d', $new->news_time) ?></span>
                    </p>
                </a>
            </li>
            <?php } ?>
        </ul>
        <a href="/news" class="reading-box-more">更多></a>
    </div>
    <?php } ?>
    <div class="data-box">
        <p class="data-box-title">平台数据</p>
        <div class="data-box-show">
            <p class="data-box-show-line1">温都金服平台已安全运营</p>
            <p class="data-box-show-line2"><span><?= (new \DateTime(date('Y-m-d')))->diff(new DateTime('2016-05-20'))->days ?></span>天（历史兑付率100%）</p>
            <p class="data-box-show-line3" style="width: auto;display: inline-block;padding: 0 .5rem"><i class="totalTradeBox" style="font-style: normal;display: none">累计投资额<span id="totalTradeAmount"></span></i>  兑付<span id="totalRefundAmount"></span>  带来<span id="totalRefundInterest"></span>元收益</p>
        </div>
    </div>
    <div class="aboutus-box swiper-container">
        <div class="swiper-wrapper">
            <a href="/site/h5?wx_share_key=h5" class="swiper-slide">
                <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/bg_aboutus1.png" alt="">
            </a>
            <a href="/news/detail?type=info&id=383" class="swiper-slide">
                <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/bg_aboutus2.png" alt="">
            </a>
        </div>
    </div>
    <a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>" class="phone-box">
        <p class="phone-box-number"><img src="<?= FE_BASE_URI ?>wap/new-homepage/images/icon_phone.png" alt=""><span class="num-1"><?= Yii::$app->params['platform_info.contact_tel'] ?></span><span class="num-2">(8:30-20:00）</span></p>
        <p class="phone-box-address">温州市鹿城区飞霞南路657号保丰大楼四层</p>
    </a>
    <img src="<?= FE_BASE_URI ?>wap/new-homepage/images/logo_wendu.png" alt="" class="logo-box">
</div>
<?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
    <?= $this->renderFile('@wap/views/layouts/footer.php')?>
<?php } ?>
    <div class="mask" style="display: none"></div>
    <!--新增首页弹框1-->
    <div class="first-popover" style="display: none">
        <img class="popover-bg" src="<?= FE_BASE_URI ?>wap/index/images/popover_01.png" alt="">
        <div class="popover-ctn">
            <img class="popover-close" src="<?= FE_BASE_URI ?>wap/index/images/popover-close.png" alt="">
            <p class="popover-title">价值288元代金券<br>已发到您的账户</p>
            <p class="popover-detail">你也可以在账户-代金券中,查看奖励</p>
            <a class="btn_01" href="/user/coupon/list">查看详情</a>
            <a class="btn_02" href="javascript:void(0)">去投资 领取160元超市卡</a>
        </div>
    </div>

    <!--新增首页弹框2-->
    <div class="second-popover" style="display: none">
        <img class="popover-close" src="<?= FE_BASE_URI ?>wap/index/images/popover-close_01.png" alt="">
        <p class="popover-title">投资前先开通资金托管账户</p>
        <p class="popover-detail">开通账户送积分，投资还有更多超市卡等你拿</p>
        <a class="popover-btn" href="/user/identity">马上去开通</a>
    </div>

<!--对应的弹框的js-->
<script>
    $('.popover-close').on('click',function(){
        $('.first-popover,.second-popover').hide();
        $('.mask').hide();
    })
</script>
<?php if (null !== $kaiPing && $kaiPing->media) { ?>
    <div class="mask hide"></div>

    <div class="pop hide">
        <img src="<?= FE_BASE_URI ?>wap/index/images/close.png" class="close_splash" alt="">
        <img src="<?= UPLOAD_BASE_URI.$kaiPing->media->uri ?>" onclick="window.location.href='<?= $kaiPing->link ?>'" alt="">
    </div>
<?php } ?>
<script>
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
        $.ajax({
            type:'get',
            url:'/site/xs',
            success:function (code) {
                //会员是否是登录状态
                if (!code.isLoggedIn) {
                    $('.newbid-box').css('display', 'block');
                }
                //判断是否是投资者
                if (code.isInvestor) {
                    if (Cookies.get('showIndexPop')) {
                        Cookies.remove('showIndexPop');
                        $('.mask,.first-popover').show();
                        $('.second-popover').hide();
                    }
                }
                //判断个人投资总额大于五万时，前端页面显示总金额
                if (code.showplatformStats) {
                    $(".totalTradeBox").css('display', 'inline-block');
                }
            }
        })
    }

    var guiZe = '<?= (empty($kaiPing) || empty($kaiPing->media)) ? '' : $kaiPing->media->uri ?>';
</script>
<script type="text/template" id="banner_text">
    <?php foreach($hotActs as $act) {
        if (!is_null($act->media)) {
            ?>
            <a class="swiper-slide" href="<?= $act->getLinkUrl() ?>"><img src="<?= UPLOAD_BASE_URI . $act->media->uri ?>" alt=""> </a>
            <?php
        }
    }
    ?>
</script>
<?php
$this->registerJs(<<<JSFILE
    $(function (){
        addToken();
        FastClick.attach(document.body);
        
        $("img").lazyload({
            threshold: 200
        });
        $('.popover-ctn .btn_02').on('click', function(){
            $('.first-popover').hide();
            $('.second-popover,.mask').show();
        });
        //瑞安渠道
        var source = Cookies.get('campaign_source');
        if (source == 'rarb') {
            $('.channel').removeClass('hide');
        }
        //开屏图
        $('.close_splash').on('click', closeAdv);
        if (guiZe) {
            if (Cookies.get('splash_show') !== guiZe) {
                document.body.addEventListener('touchmove', eventTarget);
                Cookies.set('splash_show', guiZe);
                $('.mask').removeClass('hide');
                $('.pop').removeClass('hide');
                setTimeout(closeAdv, 6000);
            }
        }
        //统计数据
        $.get('/site/stats-for-index', function (data) {
            $('#totalTradeAmount').html(Math.floor(WDJF.numberFormat(accDiv(data.totalTradeAmount, 100000000), 0))+'亿\+');
            $('#totalRefundAmount').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundAmount, 100000000), 0))+'亿\+');
            $('#totalRefundInterest').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundInterest, 100000000), 0))+'亿\+');
        });
        //判断首页上方登录状态
        checkStatus();
        
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('footer').css({'margin-bottom': 0});
        }
         //替换首页banner图
        $('#index_banner').html($('#banner_text').text());
        //轮播图
        var bannerSwiper = new Swiper('.banner-box', {
            autoplay: 3000,//可选选项，自动滑动
            speed: 500,
            loop:true,
            pagination : '.swiper-pagination',
            autoplayDisableOnInteraction: false
        });
        var aboutusSwiper = new Swiper('.aboutus-box', {
            autoplayDisableOnInteraction: false,
            slidesPerView: 2,
            spaceBetween:10
        });
    })
JSFILE
)?>
