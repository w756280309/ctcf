<?php
use yii\helpers\Html;
use common\view\AnalyticsHelper;

AnalyticsHelper::registerTo($this);
$this->registerCssFile(ASSETS_BASE_URI . 'css/bootstrap.min.css');
$this->registerCssFile(ASSETS_BASE_URI . 'promo/1607/css/smash-golden-eggs.css');
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1607/js/fastclick.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerJs('var cdn = \'' . ASSETS_BASE_URI . '\';', 1);
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1607/js/smash-golden-eggs.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 3]);
//$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
//$this->registerJsFile(ASSETS_BASE_URI . 'promo/1605/choujiang/js/weixin.js?v=20160519');//加载来源统计记录代码
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>幸运砸金蛋 - 温都金服</title>
    <meta name="keywords" content="温都金服,砸金蛋,幸运砸金蛋,PC上线助销">
    <meta name="description" content="温都金服砸金蛋活动，各种豪礼送不停">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;"/>
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
        });
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<div style="display:none;">
    <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/weitu.png" alt="" width="100%"/>
</div>
<div class="container">
    <!--banner-box-->
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/banner.png" alt="">
        </div>
    </div>
    <div class="row banner-box">
        <div class="col-xs-12">
            <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/banner1.png" alt="">
            <?php if ($model->endTime < date('Y-m-d H:i:s')) { ?>
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
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/28.png" alt="">
                                <p>28元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/50.png" alt="">
                                <p>50元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/90.png" alt="">
                                <p>90元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/120.png" alt="">
                                <p>120元代金券</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/wenhao.png" alt="" style="width: 76%;margin-left: 12%;">
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/180.png" alt="">
                                <p>180元代金券</p>
                            </div>
                        </li>
                        <li style="width: 100%;height:1px;"></li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/888.png" alt="" style="width: 84%;">
                                <p>888元礼包</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/jingdongka.png" alt="">
                                <p>100元京东卡</p>
                            </div>
                        </li>
                        <li>
                            <div>
                                <img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/zaiyici.png" alt="" style="width: 40%; margin: 0 auto;display: block;">
                                <p>再抽一次</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <img class="award-bottom" src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/bottom.png" alt="">
            <div class="ruler-box">
                <div class="ruler-title">活动规则：</div>
                <ul>
                    <li>活动期间所有用户均可获得三次砸蛋机会；</li>
                    <li>用户新增理财资产每5万可获得一次砸蛋机会；</li>
                    <li>活动期间累计投资额每满20万可额外获得一次砸蛋机会；</li>
                    <li>新增理财资产不包括回款复投；</li>
                    <li>代金券奖励当天到账，京东卡奖励客服会在7个工作日内与您联系并发放；</li>
                    <li>抽奖时间截至7月22日，过期未使用的砸蛋机会失效。</li>
                </ul>
                <div class="ruler-bottom">注：本活动最终解释权归温都金服所有。</div>
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
        <div class="card-title">获得<span>100元京东卡</span><i>一张</i></div>
        <div class="card-content">我们会在7个工作日内与您联系并发放奖励</div>
        <a href="javascript:;" class="card-btn"><img src="<?= ASSETS_BASE_URI ?>promo/1607/images/smash-golden-eggs/btn.png" alt="">
            <!--立即登录-->
            <span>确认</span>
        </a>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
