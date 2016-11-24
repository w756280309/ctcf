<?php
use yii\helpers\Html;
use common\view\WxshareHelper;

WxshareHelper::registerTo($this, $share);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
            <meta charset="UTF-8">
            <meta name="format-detection" content="telephone=no" />
            <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no"/>
            <title>温都金服 - 温都报业传媒旗下理财平台</title>
            <?= Html::csrfMetaTags() ?>
            <?php $this->head() ?>
            <link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/animate/animate.min.css?v=20161124">
            <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/css/index.css?v=20161124">
            <script src="<?= FE_BASE_URI ?>libs/lib.flexible2.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/fastclick.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/pageslider/zepto_modify.js?v=20161124"></script>
            <script src="<?= FE_BASE_URI ?>libs/pageslider/PageSlider.js?v=20161124"></script>
            <script src="<?= ASSETS_BASE_URI ?>js/js.cookie.js"></script>
            <script src="<?= ASSETS_BASE_URI ?>js/analytics.js"></script>
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

                $(function () {
                    var r = new RegExp("(^|&)" + 'hmsr' + "=([^&]*)(&|$)");
                    var result = window.location.search.substr(1).match(r);
                    if (result && result[2]) {
                        var res = Cookies.set('campaign_source', 'result[2]', {expires: 3, path:'/'});
                    }
                });

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
                <div class="page-loading-title"></div>
                <div class="page-loading-wendu"></div>
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
                                <div class="star"><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/topstar.png"><img class="star-dot" src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/star-2.png"></div>
                                <div class="title page1-title"><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/first-title.png" ></div>
                                <p class="title-h2"><span >温都金服，隶属温州报业传媒旗下的理财平台</span></p>
                                <div class="hexagon">
                                        <img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/six-fg.png" alt="六边形">
                                        <span class="hexagon-txt text-indent">平台甄选各类金融机构、优质企业理财产品。提供银行级理财服务，保障用户资金稳妥，安享稳健收益。</span>
                                </div>
                                <div class="arrow"></div>
                        </div>
                </div>
                <div class="page page3">
                        <div class="page3-title"></div>
                        <div class="padding">
                                <ul>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img1.png" alt=""></li>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img3.png" alt=""></li>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img5.png" alt=""></li>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img4.png" alt=""></li>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img6.png" alt=""></li>
                                        <li><img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img2.png" alt=""></li>
                                </ul>
                                <div class="third">
                                        <img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img7.png" alt="">
                                        <img class="center-img" src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img8.png" alt="">
                                        <img src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/wdjf-img9.png" alt="">
                                </div>
                        </div>
                        <div class="arrow"></div>
                </div>
                <!-- 第四页开始 -->
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
                        <p class="h3-txt h3-bottom">温州市鹿城区飞霞南路657号保丰大楼四层</p>
                        <h3 class="h3 h3-top">工作时间 :</h3>
                        <p class="h3-txt h3-bottom">8:30-17:30（周一至周五）</p>
                        <h3 class="h3 h3-top">客服电话 : </h3>
                        <p class="h3-txt colorC h3-bottom">400-101-5151/0577-55599998（8:30-20:00）</p>
                        <a href="/deal/deal/index" class="invited">立即理财</a>
                        <div class="code-txt">
                                <span class="left-code-txt"><i class="aline"></i>关注微信公众号<i class="aline"></i></span>
                        </div>
                        <div class="qrcode">
                                <img width="40%"  src="<?= FE_BASE_URI ?>wap/campaigns/h5-161114/img/weixin1.png" alt="qrcode">
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