<?php
use yii\helpers\Html;
use wap\assets\WapAsset;
use common\models\product\OnlineProduct;
use common\view\AnalyticsHelper;
use common\view\LoanHelper;

WapAsset::register($this);

$this->title = '温都金服';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(ASSETS_BASE_URI . 'css/swiper.min.css?v=20160419', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'js/swiper.min.js?v=20160419', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/jquery.classyloader.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/index.js?v=20160516', ['depends' => 'yii\web\JqueryAsset', 'position' => 3]);
$this->registerCssFile(ASSETS_BASE_URI . 'css/index.css?v=20160419', ['depends' => 'wap\assets\WapAsset']);  //加载在depends之后
$this->registerCssFile(ASSETS_BASE_URI . 'css/first.css?v=20160509', ['depends' => 'wap\assets\WapAsset']);

AnalyticsHelper::registerTo($this);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Yii::$app->params['wap_page_keywords'],
]);
$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::$app->params['wap_page_descritpion'],
]);

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no"/>
    <title>温都金服-温州报业传媒旗下理财平台</title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
<script>
$(function() {
    $(document).ajaxSend(function(event, jqXHR, settings) {
        var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
        if (match) {
            var val = decodeURIComponent(match[1].replace(/\+/g, " "));
            settings.url = settings.url+(settings.url.indexOf('?') >= 0 ? '&' : '?')+'token='+encodeURIComponent(val);
        }
    });

    checkLoginStatus();
});
</script>
<style>
    body {
        padding-bottom: 30px;
        background: #f7f8f8;
    }
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <?php if (!defined('IN_APP')) { ?>
    <!--header-->
    <header class="row head-title">
        <div class="logo col-xs-12 col-sm-12"><img src="<?= ASSETS_BASE_URI ?>images/logo.png" alt="logo" ></div>
        <div class="logo_tit">温州报业传媒旗下理财平台</div>
    </header>
    <?php } ?>

    <div class="row swiper-container">
        <div class="col-xs-12 swiper-wrapper">
                <?php foreach($adv as $val): ?>
                    <div class="swiper-slide"><a href="<?= $val['link'] ?>"><img src="<?= UPLOAD_BASE_URI ?>upload/adv/<?= $val['image'] ?>" alt=""></a></div>
                <?php endforeach; ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>
    <!-- 登录 注册 start -->
    <div class="row btn" id="isLoggedin" style="display: none;">
        <div class="col-sm-2"></div>
        <div class="col-xs-6 col-sm-4"><a class="signup a-btn" href="/site/signup" >注册</a></div>
        <div class="col-xs-6 col-sm-4"><a class="login a-btn" href="/site/login" >登录</a></div>
        <div class="col-sm-2"></div>
    </div>
    <!-- 登录 注册 end -->

    <!-- 推荐区start  -->
    <?php if (null !== $deal) {
        $dates = Yii::$app->functions->getDateDesc($deal->start_date);
        $rate = number_format($deal->finish_rate * 100, 0);
    ?>
    <div class="row new-box">
        <a class="new-head bot-line block" href="/deal/deal/index" >
            <div class="col-xs-8 col-sm-7 new-head-title">
                <div class="arrow-rg"></div>
                <div class="new-head-tit"><span>推荐区</span><span class="new-head-txt">优选资产，安全无忧</span></div>
            </div>
            <div class="col-xs-1 col-sm-3 "> </div>
            <div class="col-xs-3 col-sm-2 more">更多》</div>
        </a>

        <?php if (!empty($deal->jiaxi) && !$deal->isFlexRate) { ?>
        <ul class="row new-bottom" >
            <a class="block" href="/deal/deal/detail?sn=<?= $deal->sn ?>">
                <h3><?= $deal->title ?></h3>
                <li class="col-xs-6 padding-5">
                    <div class="xian1">
                        <div class="newcomer-badge"></div>
                        <span class="interest-rate"><?= rtrim(rtrim(OnlineProduct::calcBaseRate($deal->yield_rate, $deal->jiaxi), '0'), '.') ?>%</span>
                        <span class="interest-rate-add">+<?= $deal->jiaxi ?>%</span>
                        <div class="col-xs-12 percentage-txt"><span><?= \Yii::$app->params['refund_method'][$deal->refund_method] ?></span></div>
                    </div>
                </li>
                <li class="col-xs-6 padding-5">
                    <div class="new-bottom-rg">
                        <p ><span class="tishi"><?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元起投，<?= implode($deal->loanExpires, '') ?></span></p>
                        <?php if (OnlineProduct::STATUS_PRE === $deal->status) { ?>
                            <!-- 未开标 -->
                            <span class="zhuangtai weikaibiao" ><?= $dates['desc'] ?> <?= date('H:i', $deal->start_date) ?></span>
                            <?php } elseif (OnlineProduct::STATUS_NOW === $deal->status) { ?>
                            <!-- 进度 % -->
                            <div style="margin:10px 8px; border:1px solid #fe9b00; ">
                                <span class="a-progress-bg"  style="width:<?= $rate ?>%;"></span>
                                <span class="zhuangtai a-progress" ><?= $rate ?>%</span>
                            </div>
                            <?php } else { ?>
                            <div class="zhuangtai" style="margin:10px 8px; background-color: #9fa0a0;">
                                <font style="color: #fff; line-height: 34px;"><?= Yii::$app->params['deal_status'][$deal->status] ?></font>
                            </div>
                        <?php } ?>
                    </div>
                </li>
            </a>
        </ul>
        <?php } else { ?>
        <ul class="row new-bottom" >
            <a class="block" href="/deal/deal/detail?sn=<?= $deal->sn ?>">
                <h3><?= $deal->title ?></h3>
                <li class="col-xs-6 padding-5">
                    <div class="xian">
                        <span class="interest-rate <?= !$deal->isFlexRate ? '' : 'rate-steps' ?>">
                            <?= LoanHelper::getDealRate($deal) ?>%   <!--添加阶梯利率显示 -->
                        </span>
                        <div class="col-xs-12 percentage-txt"><span><i class="percentage-point"></i><?= \Yii::$app->params['refund_method'][$deal->refund_method] ?></span></div>
                    </div>
                </li>
                <li class="col-xs-6 padding-5">
                    <div class="new-bottom-rg">
                        <p ><span class="tishi"><?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元起投，<?=  implode($deal->loanExpires, '') ?></span></p>
                        <?php if (OnlineProduct::STATUS_PRE === $deal->status) { ?>
                            <!-- 未开标 -->
                            <span class="zhuangtai weikaibiao"><?= $dates['desc'] ?> <?= date('H:i', $deal->start_date) ?></span>
                            <?php } elseif (OnlineProduct::STATUS_NOW === $deal->status) { ?>
                            <!-- 进度 % -->
                            <div style="margin:10px 8px; border:1px solid #fe9b00; ">
                                <span class="a-progress-bg"  style="width:<?= $rate ?>%;"></span>
                                <span class="zhuangtai a-progress"><?= $rate ?>%</span>
                            </div>
                            <?php } else { ?>
                            <div class="zhuangtai" style="margin:10px 8px; background-color: #9fa0a0;">
                                <font style="color: #fff; line-height: 34px;"><?= Yii::$app->params['deal_status'][$deal->status] ?></font>
                            </div>
                        <?php } ?>
                    </div>
                </li>
            </a>
        </ul>
        <?php } ?>
    </div>
<!--  推荐区 end  -->
    <div class="line-box" ></div>
    <?php } ?>
<!-- 理财区start -->
    <div class="licai-box">
        <div class="licai-head">
            <div class="col-xs-8 col-sm-7 new-head-title">
                <div class="arrow-rg"></div>
                <div class="new-head-tit"><span>理财区</span><span class="new-head-txt">固定收益，稳定本息</span></div>
            </div>
            <div class="col-xs-4 col-sm-5"></div>
        </div>
        <div style="clear: both;"></div>
        <div class="row margin-add">
            <div class="col-xs-4 padding-clear"><a href="/deal/deal/index"><img src="<?= ASSETS_BASE_URI ?>images/type1.png?v=20160418" alt="温盈金" /></a> </div>
            <div class="col-xs-4 padding-clear"><a href="/deal/deal/index"><img src="<?= ASSETS_BASE_URI ?>images/type2.png?v=20160418" alt="温盈宝" /></a> </div>
            <div class="col-xs-4 padding-clear" id="wgt"><a href="/order/booking/detail?pid=1"><img src="<?= ASSETS_BASE_URI ?>images/type3.png" alt="温股投" /></a> </div>
        </div>
    </div>
    <!-- 理财区 end -->

    <!-- 最新资讯 start-->
    <?php if (!empty($news)) { ?>
    <div class="row notice-box new-box">
        <a class="new-head news-tra block" href="/news/index">
            <div class="col-xs-8 col-sm-7 new-head-title">
                <div class="arrow-rg"></div>
                <div class="new-head-tit"><span>最新资讯</span></div>
            </div>
            <div class="col-xs-1 col-sm-3 "> </div>
            <div class="col-xs-3 col-sm-2 more news-more">更多》</div>
        </a>
        <div class="notice-bottom">
            <?php foreach ($news as $key => $val) : ?>
            <a class="col-xs-12 notice <?= 0 === $key ? 'border-top' : (3 === $key ? 'border-bot' : '') ?>" href="/news/detail?id=<?= $val['id'] ?>"><span>【</span>资讯信息<span>】</span><?= $val['title'] ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php } ?>
    <!-- 最新资讯 end -->

    <!-- nav start -->
    <div class="nav-box">
        <div class="pos-rel">
            <div class="pos-fixer">
                <nav>
                    <li class="first"><a href="/">首页</a></li>
                    <li><a href="/site/about">关于我们</a></li>
                    <li><a href="/site/advantage">平台优势</a></li>
                    <li><a href="/site/help">帮助中心</a></li>
                    <li><a href="/site/contact">联系我们</a></li>
                </nav>
            </div>
        </div>
        <p>客服热线：<?= Yii::$app->params['contact_tel'] ?>（09:00-20:00）</p>
    </div>
</div>

<!--   end  -->
<!--footer-->
<?php if (!defined('IN_APP')) { ?>
<div class="row navbar-fixed-bottom footer">
    <div class="col-xs-4 footer-title">
        <div class="footer-inner">
            <a href="/" class="shouye1"><span class="shouye"></span>首页</a>
        </div>
    </div>
    <div class="col-xs-4 footer-title">
        <div class="footer-inner1">
            <a href="/deal/deal/index"><span class="licai"></span>理财</a>
        </div>
    </div>
    <div class="col-xs-4 footer-title">
        <div class="footer-inner2">
            <a href="/user/user"><span class="zhanghu"></span>账户</a>
        </div>
    </div>
</div>
<?php } ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
