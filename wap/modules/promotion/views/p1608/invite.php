<?php
use common\view\AnalyticsHelper;
use yii\helpers\Html;

AnalyticsHelper::registerTo($this);

if (!\Yii::$app->user->isGuest) {
    $this->registerJs('var cdn = \'' . (ASSETS_BASE_URI === '/' ? \Yii::$app->request->hostInfo . '/' : ASSETS_BASE_URI) . '\';', 1);
    $this->registerJs('var invite_url = \'' . \Yii::$app->request->hostInfo . '/luodiye/invite?code=' . \Yii::$app->user->getIdentity()->usercode . '\';', 1);
    $this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
    $this->registerJsFile(ASSETS_BASE_URI . 'promo/1608/js/weixin.js?v=20160805');//加载来源统计记录代码
}
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>邀请好友来挣钱，大把红包轻松拿</title>
    <meta name="keywords" content="温都金服,邀请好友,邀请,邀请奖励,代金券,现金红包">
    <meta name="description" content="邀请好友来挣钱，大把红包轻松拿">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;"/>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link href="<?= ASSETS_BASE_URI ?>css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>promo/1608/css/inviteactive.css">
    <script src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>promo/1608/js/inviteactive.js?v=20160804"></script>
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
    <div class="container">
        <!--banner-box-->
        <div class="row banner-box">
            <div class="col-xs-12">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/banner3.png" alt="">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/banner4.png" alt="">
            </div>
        </div>
        <!--invite-box-->
        <div class="row invite-box">
            <div class="col-xs-12">
                <div class="title">
                    <span><img class="left" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/left.png" alt="">邀请人奖励<img class="right" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/right.png" alt=""></span>
                </div>
                <ul class="invite-inner clearfix">
                    <li>
                        <div class="invite-quan background-yellow">
                            <div>
                                <p>30<i>元</i></p>
                                <span>(投资代金券)</span>
                            </div>
                        </div>
                        <em>好友首次投资<br/><10,000元</em>
                    </li>
                    <li>
                        <div class="invite-quan background-orange">
                            <div>
                                <p>50<i>元</i></p>
                                <span>(投资代金券)</span>
                            </div>
                        </div>
                        <em>好友首次投资<br/>>=10,000元</em>
                    </li>
                    <li>
                        <div class="invite-quan background-red">
                            <div>
                                <p><img src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/hongbao.png" alt=""></p>
                                <span>(现金红包)</span>
                            </div>
                        </div>
                        <em>好友前三次<br/>投资的0.1%</em>
                    </li>
                </ul>
            </div>
        </div>
        <!--invited-box-->
        <div class="row invited-box">
            <div class="col-xs-12">
                <div class="title">
                    <span><img class="left" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/left.png" alt="">被邀请人奖励<img class="right" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/right.png" alt=""></span>
                </div>
                <ul class="invited-inner clearfix">
                    <li>
                        <div class="invited-quan">

                        </div>
                    </li>
                    <li>
                        <div class="invited-quan background-red invited-quans">
                            <div>
                                <p>30<i>元</i></p>
                                <span>(投资代金券)</span>
                            </div>
                        </div>
                        <div class="invited-quan background-red invited-quans1">
                            <div>
                                <p>50<i>元</i></p>
                                <span>(投资代金券)</span>
                            </div>
                        </div>
                        <em>好友注册即得</em>
                    </li>
                    <li>
                        <div class="invited-quan">

                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!--rule-box-->
        <div class="row rule-boxs">
            <div class="col-xs-12">
                <div class="title">
                    <span><img class="left" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/left.png" alt="">邀请人奖励<img class="right" src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/right.png" alt=""></span>
                </div>
                <!--活动规则-->
                <div class="rule-box">
                    <div class="title"><b>活动时间:</b>2016年8月4日~2016年9月25日</div>
                    <div class="title"><b>活动规则:</b></div>
                    <ul>
                        <li>登录温都金服网站，进入“我的账户”；</li>
                        <li>点击“邀请好友”可以看到邀请好友活动，通过微信或者链接进行邀请；</li>
                        <li>当您的小伙伴通过此邀请链接注册并成功投资后，您即可获得邀请好友的奖励；</li>
                        <li>邀请人在邀请好友之前必须在平台投资过，有投资记录才能参与现金返现活动，发放奖励现金时，以"角"为单位取整，采用四舍五入；</li>
                        <li>严禁恶意刷邀请好友，如有发生，封号处理。</li>
                    </ul>
                    <div class="title"><b>奖励规则:</b></div>
                    <ul>
                        <li>被邀请好友首次单笔投资1万元以上（含1万元），邀请人获得1张50元代金券；</li>
                        <li>被邀请好友首次单笔投资1万元以下（不含1万元），邀请人获得1张30元代金券；</li>
                        <li>邀请人获得被邀请人投资额0.1% 的奖励返现（仅限前三次投资）；</li>
                        <li>被邀请人注册即可获得50元代金券。</li>
                    </ul>
                    <div class="title"><b>代金券使用规则:</b></div>
                    <ul>
                        <li>代金券有效期30天（单笔投资满1万元抵扣）。</li>
                    </ul>
                </div>
            </div>
        </div>
        <!--tishi-box-->
        <div class="row tishi-box">
            <div class="col-xs-12">
                <div>理财非存款，产品有风险，投资须谨慎</div>
            </div>
        </div>
        <!--invite-btn-->
        <div class="invite-btn <?= \Yii::$app->user->isGuest ? '' : 'invite-click' ?>" <?php if (\Yii::$app->user->isGuest) { ?> onclick="location.href='/site/login'" <?php } ?>>邀请好友</div>
        <!--share-box-->
        <div class="mark-box"></div>
        <div class="share-box">
            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/invite/share.png" alt="">
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>