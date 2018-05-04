<?php

$this->title = Yii::$app->params['pc_page_title'];

$this->registerCssFile(ASSETS_BASE_URI.'css/index.css?v=18041802', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/index.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\Html;
$user = Yii::$app->user->getIdentity();
$this->registerJs(<<<JSFILE
    $(function (){
        //统计数据
        $.get('/site/stats-for-index', function (data) {
            $('#totalTradeAmount i').html(Math.floor(WDJF.numberFormat(accDiv(data.totalTradeAmount, 100000000), 0)));
            $('#totalRefundAmount i').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundAmount, 100000000), 0)));
            $('#totalRefundInterest i').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundInterest, 100000000), 0)));
        });
       
    })
JSFILE
)
?>

<!--banner start-->
<?php if ($adv) { ?>
    <div id="banner-box">
        <!--banner 图-->
        <div class="banner-box">
            <?php foreach ($adv as $val) { ?>
                <?php if ($val->media) { ?>
                    <div class="banner" style="background-image: url('<?= UPLOAD_BASE_URI.$val->media->uri ?>');"><a href="<?= $val->link ?>" target="_blank"></a></div>
                <?php } ?>
            <?php } ?>
        </div>
        <!--选项卡-->
        <div class="banner-bottom">
            <!--半透明背景层-->
            <div class="banner-rg-bg"></div>
            <!--登录前-->
            <?php if (Yii::$app->user->isGuest) { ?>
            <div class="banner-rg loginbefore" data-status="before">
                <div class="loginbox">
                    <h4>温都金服预期年化收益率</h4>
                    <div class="login-center">
                        <h2>4<span>%</span>~9<span>%</span></h2>
                        <p class="key-txt">一千元起投，国资平台值得信赖</p>
                    </div>
                    <a href="/site/signup" class="register-btn">注册领红包</a>
                    <p class="p-login">
                        <a href="/site/login" class="login-btn">请登录</a>
                        <span>已有账号?</span>
                    </p>
                </div>
            </div>
            <?php } else { ?>
            <!--登录后-->
            <div class="banner-rg loginafter">
                <div class="loginbox">
                    <h4>欢迎来到温都金服！</h4>
                    <div class="login-center">
                        <p class="user-phone">您当前登录的账号是：</p>
                        <p class="user-phone phone-txt"><?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></p>
                    </div>
                    <a href="/user/user" class="register-btn">进入我的账户</a>
                    <p class="p-login">
                        <a href="javascript:void(0)" onclick="if(!$(this).hasClass('logout')){$(this).addClass('logout');$('#header-logout').submit();}" class="login-btn">退出登录</a>
                    </p>
                </div>
            </div>
            <?php } ?>
            <ul class="banner-btn">
                <?php foreach ($adv as $val) : ?>
                    <li><div></div></li>
                <?php endforeach; ?>
            </ul>
            <ul class="banner-btn1">
                <?php foreach ($adv as $val) : ?>
                    <li><div><span><?= $val->title ?></span></div></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php } ?>
<!--banner end-->

<!--理财公告-->
<?php if ($notice) { ?>
    <div id="licai-box">
        <div class="licai-box">
            <img src="<?= ASSETS_BASE_URI ?>images/sound.png" alt="">
            <span>理财公告</span>
            <div class="licai-lunbo">
                <?php foreach ($notice as $val) : ?>
                    <div><a href="/news/detail?type=notice&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php } ?>
<!--理财公告-->
<!--交易总额-->
<div id="turnover-box">
    <?php if (!empty($user) && $user->isShowNjq) : ?>
    <a class="njq-banner" href="/njq/connect?redirect=<?= urlencode('site/index?utm_source='.$user->campaign_source) ?>" target="_blank">
        <h4><i></i>南金中心正式入驻温都金服<u></u></h4>
        <div class="njq-img-box">
            <img src="<?= ASSETS_BASE_URI ?>images/njq_bg.png" alt="">
            <span>查看详情&gt;</span>
        </div>
    </a>
    <div class="njq-img-bottom"></div>
    <?php endif; ?>
</div>
<!--交易总额-->

<!--chengji start-->
<div class="chengji-box">

    <div class="chengji-left">
        <div class="chengji-left-bottom">
            <span>平台优势</span>
        </div>
        <div class="chengji-left-top">
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/guo.png" alt="">
                <p>国资背景</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/cup.png" alt="">
                <p>股东强势</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/tu.png" alt="">
                <p>安全合规</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/zhang.png" alt="">
                <p>产品优质</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/ling.png" alt="">
                <p>灵活便捷</p>
            </div>
        </div>
    </div>
    <div class="chengji-right">
        <div class="chengji-right-top">
            <span>媒体报道</span>
            <a href="/news/index?type=media" target="_blank">更多&gt;</a>
        </div>
        <?php if ('' !== $first_media && '' !== $first_media->pc_thumb) { ?>
        <div class="videos"><a href="/news/detail?type=media&id=<?= $first_media->id ?>" target="_blank"><img src="<?= UPLOAD_BASE_URI.$first_media->pc_thumb ?>" alt=""></a></div>
        <?php } ?>
        <ul class="chengji-right-bottom">
            <?php foreach ($media as $val) : ?>
                <li><a href="/news/detail?type=media&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!--card start-->
<!--<div class="card" id="wgt">
    <a href="/order/booking/introduction?pid=1" target="_blank">
        <img src="<?= ASSETS_BASE_URI ?>images/card.png" alt="">
    </a>
</div>
<div style="clear: both"></div>-->
<!--card end-->

<!--more start-->
<div style="clear: both"></div>
<div class="more-box ">
    <div class="more-box-left">
        <div class="more-right-top">
            <span>帮助中心</span>
            <a href="/helpcenter/operation/" target="_blank">更多&gt;</a>
        </div>
        <ul class="more-right-bottom title-ellipsis">
            <li><a href="/helpcenter/operation/" target="_blank">网站流程如何操作？</a></li>
            <li><a href="/helpcenter/security/" target="_blank">为什么在温都金服投资是安全的？</a></li>
            <li><a href="/helpcenter/background/" target="_blank">了解温都金服。</a></li>
            <li><a href="/helpcenter/product/" target="_blank">资产品种都有哪些？特点和优势是什么？</a></li>
            <li><a href="/helpcenter/contact/" target="_blank">如何联系我们？</a></li>
        </ul>
    </div>
    <div class="more-box-middle">
        <div class="more-right-top">
            <span>最新资讯</span>
            <a href="/news/index?type=info" target="_blank">更多&gt;</a>
        </div>
        <ul class="more-right-bottom title-ellipsis">
            <?php foreach ($news as $val) : ?>
                <li><a href="/news/detail?type=info&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class = "more-box-right">
        <div class = "more-right-top" style = "border-bottom: 0;">
            <span>投资榜单</span>
            <a href = " " style="display: none;">更多&gt;
            </a>
        </div>
        <div class = "more-middle">
            <div class = "more-middle-left">排名</div>
            <div class = "more-middle-middle">用户</div>
            <div class = "more-middle-right">累计投资金额(元)</div>
        </div>
        <div class = "more-bottom top-list-show"></div>
    </div>
</div>
<div style="clear: both"></div>
<!--more end-->

<!--article list-->
<div style="clear: both"></div>
<div class="more-box ">
    <div class="more-box-left">
        <div class="more-right-top">
            <span>理财指南</span>
            <a href="/news/index?type=licai" target="_blank">更多&gt;</a>
        </div>
        <ul class="more-right-bottom title-ellipsis">
            <?php foreach ($licai as $val) : ?>
                <li><a href="/news/detail?type=licai&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="more-box-middle">
        <div class="more-right-top">
            <span>投资技巧</span>
            <a href="/news/index?type=touzi" target="_blank">更多&gt;</a>
        </div>
        <ul class="more-right-bottom title-ellipsis">
            <?php foreach ($touzi as $val) : ?>
                <li><a href="/news/detail?type=touzi&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class = "more-box-right">
        <div class = "more-right-top">
            <span>理财公告</span>
            <a href="/news/index?type=notice" target="_blank">更多&gt;</a>
        </div>
        <ul class="more-right-bottom title-ellipsis">
            <?php foreach ($notice as $val) : ?>
                <li><a href="/news/detail?type=notice&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div style="clear: both"></div>
<!-- end-->

<!--股东背景 start-->
<div class="background-box">
    <div class="background-title">股东背景</div>
    <div class="background-bottom">
        <div class="background-left" style="width: 100%;text-align: center">
            <img src="<?= ASSETS_BASE_URI ?>images/logo1.png" alt="">
        </div>
        <div class="background-right" style="display: none">
            <img src="<?= ASSETS_BASE_URI ?>images/logo2-new.png" alt="">
        </div>
    </div>
</div>
<!--股东背景 end-->

<script>
    var callbackOnLogin = function() {
        location.href = "/order/booking/introduction?pid=1";
    }
    $(function() {
        $.get('/site/top-list', function(data) {
            $('.top-list-show').html(data);
        });

        //处理ajax登录
        function login() {
            //document.documentElement.style.overflow = 'hidden';   //禁用页面上下滚动效果
            //如果已经加载过登录页面，则直接显示
            if ($('.login-mark').length > 0) {
                $('.login-mark').fadeIn();
                $('.loginUp-box').fadeIn();
            } else {
                //加载登录页面
                getLoginHtml();
            }
        }
        //获取登录页面
        function getLoginHtml() {
            $.ajax({
                beforeSend: function (req) {
                    req.setRequestHeader("Accept", "text/html");
                },
                'url': '/site/login-form',
                'type': 'get',
                'dataType': 'html',
                'success': function (html) {
                    $('body').append(html);
                    $('.login-mark').fadeIn();
                    $('.loginUp-box').fadeIn();
                }
            });
            return '';
        }

        $('#wgt a').bind("click", function(e){
            if ($(this).hasClass("is_loggedin")) {
                e.preventDefault();
                login();
                return false;
            }
        });

        function checkLoginStatus()
        {
            var xhr = $.get('/site/session');
            xhr.done(function(data) {
                if (!data.isLoggedin) {
                    //未登录状态点击温股投弹出提示框
                    var wgt = $('#wgt');
                    wgt.find('a').attr("class", "is_loggedin");
                    wgt.css('cursor', 'pointer');
                }
                //判断个人投资总额大于五万时，前端页面显示总金额
//                if (data.showplatformStats) {
//                    $('.pt-data-last').removeClass('lf').addClass('rg');
//                    $('.pt-data-first').show();
//                }
            });
        }
        checkLoginStatus();
    })
</script>

