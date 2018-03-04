<?php
$this->title = '圣诞砸金蛋';
$this->share = $share;

$this->registerCssFile(ASSETS_BASE_URI . 'css/bootstrap.min.css');
$this->registerCssFile(ASSETS_BASE_URI . 'promo/1607/css/smash-golden-eggs.css');
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1607/js/fastclick.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerJs('var cdn = \'' . ASSETS_BASE_URI . '\';', 1);
$this->registerJsFile(ASSETS_BASE_URI . 'promo/161224/js/smash-golden-eggs.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 3]);

?>

<div style="display:none;">
    <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/weitu.png" alt="" width="100%"/>
</div>
<div class="container">
    <!--banner-box-->
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/banner.png" alt="">
        </div>
    </div>
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/banner1.png" alt="">
            <?php if ($model->startTime > date('Y-m-d H:i:s') || !$model->isOnline) { ?>
                <div class="banner-opportunity">活动未开始</div>
            <?php } elseif ($model->endTime < date('Y-m-d H:i:s')) { ?>
                <div class="banner-opportunity">活动已结束</div>
            <?php } elseif (\Yii::$app->user->isGuest) { ?>
                <div class="banner-opportunity"><a href='/site/login' style='color:white;'>登录马上砸蛋</a></div>
            <?php } else { ?>
                <div class="banner-opportunity">您还有<span><?= $restTicket ?>次机会</span></div>
            <?php } ?>

            <ul class="banner-inner">
                <li>
                    <div>
                        <img class="banner-egg" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/egg.png" alt="">
                        <img class="banner-lie0" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/hua.png" alt="">
                        <img class="banner-chui" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/chuizi.png" alt="">
                    </div>
                </li>
                <li>
                    <div>
                        <img class="banner-egg" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/egg.png" alt="">
                        <img class="banner-lie0" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/hua.png" alt="">
                        <img class="banner-chui" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/chuizi.png" alt="">
                    </div>
                </li>
                <li>
                    <div>
                        <img class="banner-egg" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/egg.png" alt="">
                        <img class="banner-lie0" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/liewen1.png" alt="">
                        <img class="banner-hua" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/hua.png" alt="">
                        <img class="banner-chui" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/chuizi.png" alt="">
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!--award-box-->
    <div class="row award-box">
        <div class="col-xs-12">
            <img class="award-title" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/title.png" alt="">
            <div class="award-inner">
                <div class="award-content">
                    <ul class="clearfix">
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-1.png" alt="">
                                <p>10元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-2.png" alt="">
                                <p>20元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-3.png" alt="">
                                <p>50元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-4.png" alt="">
                                <p>指环扣</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-5.png" alt="">
                                <p>888元大礼包</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-6.png" alt="">
                                <p>进口食用油</p>
                            </div>
                        </li>
                        <li style="width: 100%;height:1px;"></li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-7.png" alt="">
                                <p>福临门大米</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-8.png" alt="">
                                <p>不粘锅</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/161224/images/smash-golden-eggs/prize-9.png" alt="">
                                <p>碗筷套装</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <img class="award-bottom" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/bottom.png" alt="">
            <div class="ruler-box">
                <div class="ruler-title">活动规则：</div>
                <ul>
                    <li>活动期间所有用户均可获得1次砸蛋机会；</li>
                    <li>活动期间用户每累计投资5万元可获得1次砸蛋的机会，购买转让产品除外；</li>
                    <li>代金券奖励当天到账，实物奖励客服会在7个工作日内与您联系并发放；</li>
                    <li>活动时间12月24日-12月26日，过期未使用的砸蛋机会失效。</li>
                </ul>
                <div class="ruler-bottom">注：本活动最终解释权归楚天财富所有。</div>
            </div>
        </div>
    </div>
    <!--login-box-->
    <div class="mark-box"></div>
    <div class="login-box">
        <img class="close-img" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/close.png" alt="">
        <div class="login-img">
            <!--nologin-->
            <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/noChange.png" alt="">
        </div>
        <!--蛋壳太硬了！登录后再砸吧！-->
        <div class="login-inner">没有砸蛋机会了~ 快去投资吧！</div>
        <a href="javascript:;" class="login-btn"><img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/btn.png" alt="">
            <!--立即登录-->
            <span>去投资</span>
        </a>
    </div>
    <!--card-box-->
    <div class="card-box">
        <img class="line" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/line.png" alt="">
        <img class="close-card" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/close.png" alt="">
        <img class="card-img" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/card.png" alt="">
        <div class="card-title"></div>
        <div class="card-content">我们会在7个工作日内与您联系并发放奖励</div>
        <a href="javascript:;" class="card-btn"><img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/btn.png" alt="">
            <!--立即登录-->
            <span>确认</span>
        </a>
    </div>
</div>
