<?php

use common\view\AnalyticsHelper;
use common\view\WxshareHelper;
use yii\helpers\Html;

WxshareHelper::registerTo($this, $share);
AnalyticsHelper::registerTo($this);
$this->title = '关于我们';
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
            <meta charset="UTF-8">
            <meta name="format-detection" content="telephone=no" />
            <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no"/>
            <title><?= $this->title ?></title>
            <?= Html::csrfMetaTags() ?>
            <?php $this->head() ?>
            <link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/animate/animate.min.css?v=20161124">
            <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/h5-180211/index.css?v=2018032102">
            <script src="<?= FE_BASE_URI ?>libs/lib.flexible2.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/fastclick.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/pageslider/zepto_modify.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/pageslider/PageSlider.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>res/js/js.cookie.js"></script>
            <style>


                .page-loading .page-loading-title1{
                    width: 100%;
                    height: 0.93rem;
                    margin: 0 auto;
                    background: url(<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/first-title.png) no-repeat left top;
                    background-size: 100% 100%;
                }
                /*.page-loading .page-loading-wendu {*/
                /*    width: 6.74666666rem;*/
                /*    height: 9.746666666rem;*/
                /*    margin: 0 auto 0;*/
                /*    background: url(*/<?//= ASSETS_BASE_URI ?>/*ctcf/images/h5-180211/six-fg-new.png) no-repeat left top;*/
                /*    background-size: 100% 100%;*/
                /*}*/
            </style>
            <script>
                $(document).ready(function() {
                    FastClick.attach( document.body );
                    var num = 0;
                    window.addEventListener( "load", function() {
                        document.timer = setInterval(function(){
                            num+=1;
                            if(num == 101){
                                clearInterval(document.timer);
                            }else{
                                $(".page-redloading").css({"width":num+"%"});
                            }
                        },20);
                        setTimeout(function() {
                            $(".page-loading").hide();
                            $(".indicator").show();
                            pageslideInit();
                        }, 3000);
                    }, false);

                    var r = new RegExp("(^|&)" + 'hmsr' + "=([^&]*)(&|$)");
                    var result = window.location.search.substr(1).match(r);
                    if (result && result[2]) {
                        var res = Cookies.set('campaign_source', result[2], {expires: 3, path: '/'});
                    }
                });

                function pageslideInit()
                {
                    var pageslide = new PageSlider({
                        pages: $('.page-wrap .page'),
                        gestureFollowing: false,  //开启手势跟随
                        hasDot: false,
                        preventDefault: true,  //可选，是否阻止默认行为
                        rememberLastVisited: true,
                        animationPlayOnce: false, //可选，切换页面时，动画只执行一次
                        dev: 0, //0|1|2|3|...
                        oninit: function (e) {
                            //页面初始化
                            $('.indicator li').removeClass('cur').eq(0).addClass('cur');
                        },
                        onchange: function () {
                            //可选，每一屏切换完成时的回调
                            $(".page-wrap>div").each(function(){
                                var $index = $(this).index();
                                if($(this).hasClass("current")){
                                    $('.indicator li').removeClass('cur').eq($index).addClass('cur');
                                }
                            });
                        }
                    });
                }

                $(document).on('ajaxSend', function(event, jqXHR, settings) {
                        var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
                        if (match) {
                                var val = decodeURIComponent(match[1].replace(/\+/g, " "));
                                settings.url = settings.url+(settings.url.indexOf('?') >= 0 ? '&' : '?')+'token='+encodeURIComponent(val);
                        }
                });
            </script>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="page page-loading" data-lock-next="true" data-lock-prev="true">
                <div class="top-empt-loading"></div>
                <div class="page-loading-title1"></div>
                <div class="page-loading-wendu">系湖北日报新媒体集团旗下公司</div>
                <div class="page-loading-jindubg">
                        <div class="page-redloading-wrap">
                                <div class="page-redloading"></div>
                        </div>
                </div>
                <div class="loading-title">Loading</div>
        </div>
        <div class="page-wrap">
                <div class="page page1" data-lock-prev="true"  style="-webkit-overflow-scrolling: touch;">
                        <div class="page__inner" style="position: relative; height: 100%">
                                <div class="star">
                                    <img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/topstar.png">
                                    <img class="star-dot" src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/star-2.png">
                                </div>
                                <div class="title page1-title"><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/first-title.png" alt=""></div>
                                <p class="title-h2"><span >系湖北日报新媒体集团旗下公司</span></p>
                                <div class="hexagon">
                                        <img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/six-fg-new.png" alt="六边形">
                                        <span class="hexagon-txt" style="top: -23px;left: 26.6%;width: 51%;">平台以专业的风控能力、高效的技术保障为用户提供便捷的互联网金融综合信息服务。</span>
                                </div>
                                <div class="arrow"></div>
                        </div>
                </div>
                <!-- 第三页开始 -->
                <div class="page page4" style="-webkit-overflow-scrolling: touch;">
                    <div class="page__inner" style="position: relative; height: 100%">
                        <div class="top-empt"></div>
                        <div class="page4-title page-title">
                        </div>
                        <div class="top-empt4-1"></div>
                        <div class="top-empt4-2"></div>
                        <ul class="page4-content page-content">
                            <li class="page4-content1"></li>
                            <li class="page4-content2"></li>
                            <li class="page4-content3"></li>
                            <li class="page4-content4"></li>
                            <li class="page4-content5" style="margin-bottom:0;"></li>
                        </ul>
                        <div class="arrow"></div>
                    </div>
                </div>
                <!-- 第四页开始 -->
                <div class="page page3">
                        <div class="page3-title"></div>
                        <div class="padding">
                                <ul>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img1.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img2.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img3.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img4.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img5.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img6.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img7.png" alt=""></li>
                                        <li><img src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/ctcf-img8.png" alt=""></li>
                                </ul>
<!--                                <div class="third">-->
<!--                                        <img src="--><?//= FE_BASE_URI ?><!--wap/campaigns/h5-161114/img/wdjf-img7.png" alt="">-->
<!--                                        <img class="center-img" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/h5-161114/img/wdjf-img8-new.png" alt="">-->
<!--                                        <img src="--><?//= FE_BASE_URI ?><!--wap/campaigns/h5-161114/img/wdjf_img9_new.png" alt="">-->
<!--                                </div>-->
                        </div>
                        <div class="arrow"></div>
                </div>
                <div class="page page5">
                        <div class="top-empt"></div>
                        <div class="page5-title page-title"></div>
                        <div class="top-empt5-1"></div>
                        <div class="top-empt5-2"></div>
                        <div class="page5-content page-content">
                                <div class="page5-center-br"></div>
                                <div class="page5-center-s"></div>
                                <div class="page5-center-b"></div>
                                <div class="page5-content1 slide-In-Down1"></div>
                                <div class="page5-content2 slide-In-Down2"></div>
                                <div class="page5-content3 slide-In-Down4"></div>
                                <div class="page5-content4 slide-In-Down3"></div>
                        </div>
                        <div class="arrow"></div>
                </div>
                <div class="page page6" >
                        <div class="page6-title page-title title-top"></div>
                        <h3 class="h3 h3-top">公司地址 :</h3>
                        <p class="h3-txt h3-bottom">武汉市武昌区东湖路181号楚天文化创意产业园 8号楼1层</p>
                        <h3 class="h3 h3-top">工作时间 :</h3>
                        <p class="h3-txt h3-bottom">9:00-17:30（周一至周五）</p>
                        <h3 class="h3 h3-top">客服电话 : </h3>
                        <p class="h3-txt colorC h3-bottom"><?= Yii::$app->params['platform_info.contact_tel'] ?>（9:00-20:00）</p>
                        <a href="/deal/deal/index" class="invited">立即出借</a>
                        <div class="code-txt">
                            <span class="left-code-txt"><i class="aline"></i>关注微信公众号<i class="aline"></i></span>
                        </div>
                        <div class="qrcode">
                            <img width="40%"  src="<?= ASSETS_BASE_URI ?>ctcf/images/h5-180211/weixin1.png" alt="qrcode">
                        </div>
                </div>
        </div>

        <ul class="indicator">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
        </ul>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
