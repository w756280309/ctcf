<?php
use yii\helpers\Html;
use common\view\AnalyticsHelper;

wap\assets\WapAsset::register($this);
AnalyticsHelper::registerTo($this);
$this->registerCssFile(ASSETS_BASE_URI . 'promo/1605/choujiang/css/520.css?v=20150', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1605/choujiang/js/520.js?v=20180118', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);//加载来源统计记录代码
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1605/choujiang/js/weixin.js?v=20160519');//加载来源统计记录代码
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no"/>
    <title>楚天财富 - 仲夏狂欢送现金，最高288元</title>
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
        <div class="container">
            <div class="icon">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_1.png?v=1.0" alt="广告图">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_2.png?v=1.0" alt="广告图">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_3.png?v=1.0" alt="广告图">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_4.png?v=1.0" alt="广告图">
                <p class="clear"></p>
            </div>
            <div class="title_box row">
                <div class="col-xs-1"></div>
                <div class="col-xs-10 title_cn">
                    楚天财富正式上线有金喜,理财代金券注册即抽
                </div>
                <div class="col-xs-1"></div>
            </div>
            <div class="icon">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_6.png?v=1.0" alt="广告图">
                <p class="clear"></p>
            </div>

            <div class="center_box row">
                <div class="col-xs-2"></div>
                <div class="col-xs-8 center_box_tip">
                    <?php if (!$endFlag) { ?>
                        <div id="no1" class="hid e">
                            <p class="tip1">楚天财富起航有金喜，看看你能得多少？</p>
                            <input type="text" class="input_txt" id="phone" placeholder="请输人手机号" maxlength="11">
                            <input type="button" class="input_btn draw" value="立即抽奖">
                        </div>

                        <div id="no2">
                            <p class="tip2 tipbolder">恭喜您，豪中<span class="tip3 tipbolder">248元</span>！</p>
                            <p class="tip2">即刻起航财富新高度，我们的鼓励是真金白银</p>
                            <input type="button" class="input_btn blue_btn draw" value="再抽一次">
                            <input type="button" class="input_btn register" value="立即注册赚起来">
                        </div>

                        <div id="no3">
                            <p class="tip2 tipbolder">真出彩，抽中<span class="tip3 tipbolder">268元</span>！</p>
                            <p class="tip2">金光闪闪的红包,尽然这么多</p>
                            <input type="button" class="input_btn blue_btn draw" value="再抽一次">
                            <input type="button" class="input_btn register" value="立即注册赚起来">
                        </div>

                        <div id="no4">
                            <p class="tip2 tipbolder">棒极啦，<span class="tip3 tipbolder">288元</span>已擒获</p>
                            <p class="tip2">努力没白费,登顶最高奖</p>
                            <p class="tip2 tip4">已经记录您领取的最高奖,请立即注册</p>
                            <input type="button" class="input_btn register" value="立即注册赚起来">
                        </div>

                        <div id="no5">
                            <p class="tip2 tipbolder">真情回馈老用户</p>
                            <p class="tip2 tipbolder">惊喜奖励不含糊</p>
                            <p class="tip2 tip4 tiponly">最高奖券<span class="tip3 tiponly">288元</span>,已发放到您的账户</p>
                            <input type="button" class="input_btn login" value="登录查看">
                        </div>

                        <div id="no6">
                            <p class="tip2"></p>
                            <p class="tip2 tipbolder">您已领过,请用本手机号登录账户中心查看</p>
                            <p class="tip2"></p>
                            <input type="button" class="input_btn login" value="登录查看">
                        </div>
                    <?php } else { ?>
                        <div id="no7">
                            <p class="tip2 tip5">我们等的花儿都谢了,您却在活动结束后才看</p>
                            <p class="tip2 tip5 ">请您去看看其他活动吧</p>
                            <input type="button" class="input_btn" id="index" value="回到首页">
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xs-2"></div>
            </div>
            <div class="icon">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_8.png?v=1.0" alt="广告图">
                <img src="<?= ASSETS_BASE_URI ?>promo/1605/choujiang/images/img_520_81.png?v=1.0" alt="广告图">
                <p class="clear"></p>
            </div>
            <div class="p_box row">
                <div class="col-xs-12">
                    <p class="informations"><span class="one">1</span>活动时间：2016年5月20日至2016年6月10日;</p>
                    <p class="informations"><span class="two">2</span>新老用户均可参与,领券和注册时手机号须一致；</p>
                    <p class="informations"><span class="three">3</span>每笔出借限用一张券,使用时请参看代金券规则；</p>
                    <p class="informations"><span class="four">4</span>领券后6月20日前未使用,代金券失效；</p>
                    <p class="informations"><span class="five">5</span>代金券等同于现金,与出借本金一并计息，</p>
                    <p class="informations only"><i class="five_only"></i>随项目还款时返还；</p>
                    <p class="informations"><span class="six">6</span>法律许可范围内,本活动最终解释权归</p>
                    <p class="informations only"><i class="six_only"></i>楚天财富所有。</p>
                </div>
                <p class="clear"></p>
            </div>
            <p class="clear"></p>
            <div class="icon">
                <div class="foot">理财非存款，产品有风险，出借需谨慎</div>
            </div>
            <div class="row">
                <div class="col-xs-4"></div>
                <div class="col-xs-4">
                    <a class="home" href="/">返回首页</a>
                </div>
                <div class="col-xs-4"></div>
            </div>
        </div>
        <form></form>
    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
