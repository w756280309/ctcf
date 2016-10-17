<?php
$this->title = '温都金服';
$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$this->showBottomNav = true;

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use wap\assets\WapAsset;
use yii\web\JqueryAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/swiper.min.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/wap_index.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/kaipin.css', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/swiper.min.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'js/lib.flexible/lib.flexible.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'js/index.js', ['depends' => JqueryAsset::class]);
?>

<div class="mask" id="mask_kaiping" style="position: fixed; z-index: 2000; display: none;"></div>
<div class="tail" style="display: none;">
    <div class="tail_img_top"></div>
    <a class="tail_close">
        <img src="<?= ASSETS_BASE_URI ?>images/kaiping_02.png" alt="开屏图">
    </a>
    <div class="tail_img_bottom">
        <img src="<?= ASSETS_BASE_URI ?>images/kaiping_08.png" alt="开屏图">
    </div>
</div>

<!--banner开始-->
<div class="row swiper-container">
    <div class="col-xs-12 swiper-wrapper">
        <?php foreach($adv as $val) : ?>
            <div class="swiper-slide"><a href="<?= $val->link ?>"><img src="<?= UPLOAD_BASE_URI ?>upload/adv/<?= $val->image ?>" alt=""></a></div>
        <?php endforeach; ?>
    </div>
    <!-- Add Pagination -->
    <div class="swiper-pagination"></div>
</div>
<!--banner结束-->

<!-- 公告开始 -->
<div class="row index-notice">
    <?php if (!empty($news)) : ?>
        <div class="index-notice-top clearfix">
            <div class="index-notice-left lf clearfix">
                <img class="lf" src="<?= ASSETS_BASE_URI ?>images/index/notice.png" alt="">
                <div class="index-notices lf">
                    <?php foreach ($news as $key => $val) : ?>
                        <div><a href="/news/detail?id=<?= $val->id ?>"><?= $val->title ?></a></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="index-notice-right rg">
                <a href="/news/index">更多<img src="<?= ASSETS_BASE_URI ?>images/index/arrow.png" alt=""></a>
            </div>
        </div>
    <?php endif; ?>

    <div class="index-notice-bottom">
        <ul class="index-notice-bottom-bg">
            <li class="lf">
                <a href="/site/about">
                    <img src="<?= ASSETS_BASE_URI ?>images/index/intro.png" alt="">
                    <p>温都品牌介绍</p>
                </a>
            </li>
            <li class="rg">
                <a href="/site/advantage">
                    <img src="<?= ASSETS_BASE_URI ?>images/index/strand.png" alt="">
                    <p>严苛风控标准</p>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- 公告结束 -->

<!--精品推荐开始-->
<div class="index-recommend">
    <div class="index-recommend-top clearfix">
        <div class="index-recommend-left lf clearfix">
            <img class="lf" src="<?= ASSETS_BASE_URI ?>images/index/recomd.png" alt="">
            <p>精品推荐</p>
        </div>
        <div class="index-recommend-right rg">
            <a href="/deal/deal/index">更多<img src="<?= ASSETS_BASE_URI ?>images/index/arrow.png" alt=""></a>
        </div>
    </div>
    <a href="/deal/deal/detail?sn=<?= $deal->sn ?>">
        <div class="index-recommend-bottom">
            <h2><?= $deal->title ?></h2>
            <ul class="clearfix">
                <li class="lf">
                    <?php if (!empty($deal->jiaxi) && !$deal->isFlexRate) { ?>
                        <span><?= StringUtils::amountFormat2(OnlineProduct::calcBaseRate($deal->yield_rate, $deal->jiaxi)) ?></span><em>%</em><em class="index-recommend-crea">+<?= StringUtils::amountFormat2($deal->jiaxi) ?>%</em>
                    <?php } else { ?>
                        <span><?= LoanHelper::getDealRate($deal) ?></span><em>%</em>
                    <?php } ?>
                    <p>预期年化</p>
                </li>
                <li class="lf">
                    <?php $ex = $deal->getDuration() ?>
                    <span><?= $ex['value'] ?></span><em><?= $ex['unit'] ?></em>
                    <p>期限</p>
                </li>
                <li class="lf">
                    <span><?= StringUtils::amountFormat2($deal->start_money) ?></span><em>元</em>
                    <p>起投金额</p>
                </li>
            </ul>
            <div class="recommend-progress">
                <?php $rate = OnlineProduct::STATUS_FOUND === $deal->status ? 100 : number_format($deal->finish_rate * 100, 0); ?>
                <span class="recommend-all"><span class="recommend-pro" style="width: <?= $rate ?>%;"></span></span>
                <em class="rg"><?= $rate ?>%</em>
            </div>

            <div class="index-recommend-notice">
                <img src="<?= ASSETS_BASE_URI ?>images/index/dot.png" alt="">
                <span><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></span>
            </div>
        </div>
    </a>
</div>
<!--精品推荐结束-->

<!--股东背景开始-->
<div class="shareholder">
    <h2>股东背景</h2>
    <div class="shareholder-m">
        <span></span>
        <img src="<?= ASSETS_BASE_URI ?>images/index/king.png" alt="">
        <span></span>
    </div>

    <div class="shareholder-b clearfix">
        <img class="lf" src="<?= ASSETS_BASE_URI ?>images/index/wz.png" alt="">
        <img class="rg" src="<?= ASSETS_BASE_URI ?>images/index/nj.png" alt="">
    </div>
</div>
<!--股东背景结束-->

<!--bottom开始-->
<div class="index-bottom">
    <div class="swiper-container1">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <dl class="clearfix">
                    <dt class="lf"><img src="<?= ASSETS_BASE_URI ?>images/index/icon1.png" alt=""></dt>
                    <dd class="lf">
                        <p>收益稳健</p>
                        <span>预期年化5.5~9%</span>
                    </dd>
                </dl>
            </div>
            <div class="swiper-slide">
                <dl class="clearfix">
                    <dt class="lf"><img src="<?= ASSETS_BASE_URI ?>images/index/icon2.png" alt=""></dt>
                    <dd class="lf">
                        <p>产品优质</p>
                        <span>金融、政府类产品</span>
                    </dd>
                </dl>
            </div>
            <div class="swiper-slide">
                <dl class="clearfix">
                    <dt class="lf"><img src="<?= ASSETS_BASE_URI ?>images/index/icon3.png" alt=""></dt>
                    <dd class="lf">
                        <p>资金安全</p>
                        <span>第三方资金托管</span>
                    </dd>
                </dl>
            </div>
            <div class="swiper-slide">
                <dl class="clearfix">
                    <dt class="lf"><img src="<?= ASSETS_BASE_URI ?>images/index/icon4.png" alt=""></dt>
                    <dd class="lf">
                        <p>门槛较低</p>
                        <span>1000元即可投资</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<!--bottom结束-->

<script>
    function closeAdv()
    {
        $('#mask_kaiping,.tail').hide();
        $('#mask_kaiping').hide();
        $('html').removeAttr('ontouchmove');
    }
    $(function() {
        if ($.cookie('splash_show') !== "1") {
            $('html').attr('ontouchmove','event.preventDefault()');
            $.cookie('splash_show', "1");
            $('#mask_kaiping').show();
            $('#mask_kaiping,.tail').show();
            setTimeout(closeAdv, 4000);
            $('.tail_close').on('click', closeAdv);
        }
    })
</script>