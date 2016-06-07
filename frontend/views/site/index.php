<?php
$this->title = Yii::$app->params['pc_page_title'];

$this->registerCssFile(ASSETS_BASE_URI.'css/index.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/index.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
?>

<!--banner start-->
<?php if ($adv) { ?>
    <div id="banner-box">
        <!--banner 图-->
        <div class="banner-box">
            <?php foreach ($adv as $val) : ?>
                <div class="banner" style="background: url('/upload/adv/<?= $val->image ?>') no-repeat center center;"><a href="<?= $val->link ?>"></a></div>
            <?php endforeach; ?>
        </div>
        <!--选项卡-->
        <div class="banner-bottom">
            <ul class="banner-btn">
                <?php foreach ($adv as $val) : ?>
                    <li><div></div></li>
                <?php endforeach; ?>
            </ul>
            <ul class="banner-btn1">
                <?php foreach ($adv as $val) : ?>
                    <li><div><?= $val->title ?></div></li>
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
                    <div><a href=""><?= $val->title ?></a></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php } ?>
<!--理财公告-->

<!--chengji start-->
<div class="chengji-box">
    <div class="chengji-left">
        <div class="chengji-left-bottom">
            <span>平台优势</span>
        </div>
        <div class="chengji-left-top">
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/cup.png" alt="">
                <p>国资背景</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/zhang.png" alt="">
                <p>谷东强势</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/tu.png" alt="">
                <p>安全合规</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/tu.png" alt="">
                <p>产品优质</p>
            </div>
            <div>
                <img src="<?= ASSETS_BASE_URI ?>images/tu.png" alt="">
                <p>灵活便捷</p>
            </div>
        </div>
    </div>
    <div class="chengji-right">
        <div class="chengji-right-top">
            <span>媒体报道</span>
            <a href="">更多&gt;</a>
        </div>
<!--        <div class="videos"><img src="<?= ASSETS_BASE_URI ?>images/video-bg2.jpg" alt=""></div>-->
        <ul class="chengji-right-bottom">
            <?php foreach ($media as $val) : ?>
                <li><a href=""><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!--推荐标 start-->
<?php if ($loans) { ?>
    <div class="new-heade"><span>推荐标</span><a href="">更多&gt;</a></div>
    <div class="yingshou-box">
        <div class="yingshou-content1">
            <ul class="yingshou-inner1">
                <?php foreach ($loans as $val) : ?>
                    <li>
                        <a href="" class="yingshou-left yingshou <?= (!in_array($val->status, [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW])) ? 'huibian' : '' ?>">
                            <div class="yingshou-top"><span><?= $val->title ?></span></div>
                            <?php if (!in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) { ?>
                                <div style="clear: both"></div>
                                <div class="yingshou-content">
                                    <p>融资金额：<?= rtrim(rtrim(number_format($val->money, 2), '0'), '.') ?>元</p>
                                    <p>可投金额：<?= rtrim(rtrim(number_format($val->money - $val->funded_money, 2), '0'), '.') ?>元</p>
                                    <div><?= Yii::$app->params['refund_method'][$val->refund_method] ?></div>
                                </div>
                            <?php } else { ?>
                                <div class="yingshou-content2">
                                    <p class="yingshou-content2-left">年化利率
                                        <span>
                                            <?php
                                                echo rtrim(rtrim(number_format(OnlineProduct::calcBaseRate($val->yield_rate, $val->jiaxi), 2), '0'), '.');
                                                if ($val->isFlexRate && $val->rateSteps) {
                                                    echo '~'.rtrim(rtrim(number_format(RateSteps::getTopRate(RateSteps::parse($val->rateSteps)), 2), '0'), '.');
                                                } elseif ($val->jiaxi) {
                                                    echo '+'.rtrim(rtrim(number_format($val->jiaxi, 2), '0'), '.');
                                                }
                                            ?>%
                                        </span></p>
                                    <p class="yingshou-content2-right">项目期限 <span><?= $val->expires.(1 === (int) $val->refund_method ? "天" : "个月") ?></span></p>
                                </div>
                            <?php } ?>
                            <div class="yingshou-jindu">
                                <div class="yingshou-jindutiao">
                                    <div class="yingshou-jindutiao1" progressbar="<?= number_format($val->finish_rate * 100) ?>"></div>
                                </div>
                                <span><?= number_format($val->finish_rate * 100) ?>%</span>
                            </div>
                            <div style="clear: both"></div>
                            <?php if (!in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) { ?>
                                <div class="yingshou-number">
                                    <div class="yingshou-nian">
                                        <div>
                                            <em>
                                                <?= rtrim(rtrim(number_format(OnlineProduct::calcBaseRate($val->yield_rate, $val->jiaxi), 2), '0'), '.') ?>
                                                <?php if ($val->isFlexRate && $val->rateSteps) { ?>
                                                    ~<?= rtrim(rtrim(number_format(RateSteps::getTopRate(RateSteps::parse($val->rateSteps)), 2), '0'), '.') ?><span>%</span><i></i>
                                                <?php } elseif ($val->jiaxi) { ?>
                                                    <span>%</span><i>+rtrim(rtrim(number_format($val->jiaxi, 2), '0'), '.')%</i>
                                                <?php } else { ?>
                                                    <span>%</span>
                                                <?php } ?>
                                            </em>
                                        </div>
                                        <p>年化利率</p>
                                    </div>
                                    <div class="yingshou-nian yingshou-xian">
                                        <?= $val->expires ?><span><?= 1 === (int) $val->refund_method ? "天" : "个月" ?></span>
                                        <p>项目期限</p>
                                    </div>
                                </div>
                                <div class="yingshou-liji">
                                    <?php
                                        if (OnlineProduct::STATUS_PRE === $val->status) {
                                            $dates = Yii::$app->functions->getDateDesc($val->start_date);
                                    ?>
                                        <div class="yingshou-liji"><?= $dates['desc'].date('H:i', $dates['time']) ?></div>
                                    <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                                        <div class="yingshou-liji">立即投资</div>
                                    <?php } else { ?>
                                        <div class="yingshou-manbiao"><?= \Yii::$app->params['deal_status'][$val->status] ?></div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="yingshou-shouyi"><?= \Yii::$app->params['deal_status'][$val->status] ?></div>
                                <img src="<?= ASSETS_BASE_URI ?>images/shouyizhong_icon.png" class="yingshou-shouyi-img" alt="">
                            <?php } ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="clear: both"></div>
    </div>
<?php } ?>
<!--推荐标 end-->

<!--card start-->
<div class="card">
    <a href="">
        <img src="<?= ASSETS_BASE_URI ?>images/card.png" alt="">
    </a>
</div>
<div style="clear: both"></div>
<!--card end-->

<!--more start-->
<div style="clear: both"></div>
<div class="more-box ">
    <div class="more-box-left">
        <div class="more-right-top">
            <span>帮助中心</span>
            <a href="">更多&gt;</a>
        </div>
        <ul class="more-right-bottom">
            <li><a href="">网站流程如何操作？</a></li>
            <li><a href="">为什么在温都金服投资是安全的？</a></li>
            <li><a href="">了解温都金服。</a></li>
            <li><a href="">资产品种都有哪些？特点和优势是什么？</a></li>
            <li><a href="">如何联系我们？</a></li>
        </ul>
    </div>
    <div class="more-box-middle">
        <div class="more-right-top">
            <span>最新资讯</span>
            <a href="">更多&gt;</a>
        </div>
        <ul class="more-right-bottom">
            <?php foreach ($news as $val) : ?>
                <li><a href=""><?= $val->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class = "more-box-right">
        <div class = "more-right-top" style = "border-bottom: 0;">
            <span>投资榜单</span>
            <a href = "">更多&gt;
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

<!--股东背景 start-->
<div class="background-box">
    <div class="background-title">股东背景</div>
    <div class="background-bottom">
        <div class="background-left">
            <img src="<?= ASSETS_BASE_URI ?>images/logo1.png" alt="">
        </div>
        <div class="background-right">
            <img src="<?= ASSETS_BASE_URI ?>images/logo2.png" alt="">
        </div>
    </div>
</div>
<!--股东背景 end-->

<script>
    $(function() {
        $.get('/site/top-list', function(data) {
            $('.top-list-show').html(data);
        });
    })
</script>