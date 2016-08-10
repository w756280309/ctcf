<?php
use common\view\AnalyticsHelper;
use yii\helpers\Html;

AnalyticsHelper::registerTo($this);

$this->registerJs('var cdn = \'' . (ASSETS_BASE_URI === '/' ? \Yii::$app->request->hostInfo . '/' : ASSETS_BASE_URI) . '\';', 1);
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1608/js/olywx.js');//加载来源统计记录代码
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>新手迎奥运，投资赢大奖</title>
    <meta name="keywords" content="温都金服,理财,P2P,奥运,奥运活动,投资奖励">
    <meta name="description" content="新手迎奥运，投资赢大奖">
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link href="<?= ASSETS_BASE_URI ?>css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>promo/1608/css/olympic.css">
    <script src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>promo/1608/js/olympic.js"></script>
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
    <!--用户类型-->
    <input type="hidden" name="status" id="status" value="<?= $res ?>">
    <div class="container">
        <!--banner-box-->
        <div class="row banner-box">
            <div class="col-xs-12">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/banner1.png" alt="">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/banner2.png" alt="">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/banner3.png" alt="">
            </div>
        </div>
        <!--help-box-->
        <div class="row help-box">
            <div class="col-xs-12">
                <ul class="clearfix zhuli">
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/pingpang.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/swim.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/quanji.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/paiqiu.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li>
                        <div class="aoyun">
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/aoyun.png" alt="">
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/lanqiu.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/run.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/fooerbal.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                    <li class="help-btn">
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/wangqiu.png" alt="">
                            <p>助力</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!--rule-box-->
        <div class="row rule-box">
            <div class="col-xs-12">
                <div class="rule-title">活动规则：</div>
                <ul>
                    <li class="clearfix">
                        <span>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/icon.png" alt="">
                        </span>
                        <p>活动时间：2016年8月12日~8月31日</p>
                    </li>
                    <li class="clearfix">
                        <span>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/icon.png" alt="">
                        </span>
                        <p>活动期间，未投资过温都金服的用户可选择自己喜爱的运动项目进行助力，单笔成功投资1万元即可获得相应的实物奖励；</p>
                    </li>
                    <li class="clearfix">
                        <span>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/icon.png" alt="">
                        </span>
                        <p>每人只可领取一次奖励，奖品将于10个工作日内发货，请填写好地址信息并保持手机畅通；</p>
                    </li>
                    <li class="clearfix">
                        <span>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/icon.png" alt="">
                        </span>
                        <p>在法律允许范围内，本活动最终解释权归温都金服所有；</p>
                    </li>
                    <li class="clearfix">
                        <span>
                            <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/icon.png" alt="">
                        </span>
                        <p>本活动和苹果公司无关。</p>
                    </li>
                </ul>
            </div>
        </div>
        <!--mark-box-->
        <div class="mark-box"></div>
        <!--奖品弹窗-->
        <div class="award-box">
            <div class="award-top">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/top.jpg" alt="">
                <img class="close-box" src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/close.png" alt="">
            </div>
            <div class="award-inner">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/shouhuan.jpg" alt="">
                <div>立即领取</div>
            </div>
        </div>
        <!--address-box-->
        <!--地址弹窗-->
        <div class="address-box">
            <div class="address-top">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/top.jpg" alt="">
                <img class="close-address" src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/close.png" alt="">
            </div>
            <div class="address-inner">
                <div class="address-title">地址：</div>
                <form id="form" action="/promotion/p1608/add-user-address">
                    <input name="_csrf" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="Promo0809Log[prize_id]" id="type" value="<?= $log ? $log->prize_id : 0 ?>">
                    <textarea name="Promo0809Log[user_address]" id="address"></textarea>
                </form>
                <div class="save-address">保存地址</div>
            </div>
        </div>
        <!--invite-box-->
        <!--领取成功弹窗-->
        <div class="invite-box">
            <div class="invite-top">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/top.jpg" alt="">
                <img class="close-invite" src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/close.png" alt="">
            </div>
            <div class="invite-inner">
                <span>
                    恭喜您！领取成功！<br/>
                    我们将在10个工作日内<br/>发货！<br/>
                    您也可以邀请好友参加活动！领取<br/>邀请奖<br/>励！
                </span>
                <div class="invite-btn">立即邀请</div>
            </div>
        </div>
        <!--login-box-->
        <!--登录弹窗-->
        <div class="login-box">
            <div class="login-top">
                <img src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/top.jpg" alt="">
                <img class="close-login" src="<?= ASSETS_BASE_URI ?>promo/1608/images/olympic/close.png" alt="">
            </div>
            <div class="login-inner">
                登录才能助力哦~~
                <div class="login-btn" onclick="location.href='/site/login'">立即登录</div>
            </div>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>