<?php

$this->title = Yii::$app->params['pc_page_title'];

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/index.min.css?v=1.2', ['depends' => 'frontend\assets\CtcfFrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/mask/mask.min.css?v=1.3', ['depends' => 'frontend\assets\CtcfFrontAsset']);
//$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/jquery-1.11.1.min.js', ['depends' => 'frontend\assets\CtcfFrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/jquery.SuperSlide.2.1.1.js', ['depends' => 'frontend\assets\CtcfFrontAsset', 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/handlebars-v4.0.11.js', ['depends' => 'frontend\assets\CtcfFrontAsset', 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/mask/mask.js', ['depends' => 'frontend\assets\CtcfFrontAsset', 'position' => 1]);

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\Html;
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
<div class="banner">
    <div class="hd">
        <ul class="clear-fix">
            <?php foreach ($adv as $val) : ?>
                <li class="lf"></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="bd">
        <ul>
            <?php foreach ($adv as $val) { ?>
                <?php if ($val->media) { ?>
                    <li><a href="<?= $val->link ?>" target="_blank"><img src="<?= UPLOAD_BASE_URI.$val->media->uri ?>" alt=""></a></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
    <div class="ctn-area">
        <?php if (Yii::$app->user->isGuest) { ?>
        <!--未登录-->
        <div class="login-box">
            <p class="fz22 fz-white mgt40b30">最高年化收益率</p>
            <p class="fz60 fz-yellow">12%</p>
            <a class="fz18 fz-black btn-yellow mgt28b8" href="/site/signup">立即注册</a>
            <p class="fz14 fz-white">已有账户，立即<a class="fz-yellow" href="/site/login">登录</a></p>
        </div>
        <?php } else { ?>
        <!--已登录-->
        <div class="login-box">
            <p class="fz22 fz-white mgt40b17">欢迎来到楚天财富！</p>
            <img class="w88" src="<?= ASSETS_BASE_URI ?>ctcf/images/avatar.png" alt="">
            <p class="fz14 fz-white mgt18b14">您当前登录的账号是：<?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></p>
            <a class="fz18 fz-black btn-yellow" href="/user/user">进入我的资产</a>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<!--affiche-->
<?php if ($notice) { ?>
<div class="affiche clear-fix">
    <div class="inner-affiche">
        <div class="lf clear-fix fz14">
            <div class="lf">
                <img class="mgr20 w21" src="<?= ASSETS_BASE_URI ?>ctcf/images/sound.png" alt=""><i class="fz-black">【平台公告】</i>
            </div>
            <div class="txtScroll-top lf">
                <div class="bd">
                    <ul>
                        <?php foreach ($notice as $val) : ?>
                            <li><a class="fz-black" href="/news/detail?type=notice&id=<?= $val->id ?>" target="_blank"><?= $val->title ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <a class="rg fz14 header-hover" href="/news/index?type=notice">更多>></a>
    </div>
</div>
<?php } ?>
<!--container-->
<div class="ctcf-container">
    <!--平台安全保障-->
    <div class="intro mgt20 clear-fix">
        <dl class="lf clear-fix">
            <dt class="lf mgr20"><img class="w59" src="<?= ASSETS_BASE_URI ?>ctcf/images/t001@2x.png" alt=""></dt>
            <dd class="lf fz-black"><p class="fz30 mgtb10">国有平台</p><p class="fz14">湖北省国有传媒旗下机构</p></dd>
        </dl>
        <dl class="lf clear-fix">
            <dt class="lf mgr20"><img class="w59" src="<?= ASSETS_BASE_URI ?>ctcf/images/t002@2x.png" alt=""></dt>
            <dd class="lf fz-black"><p class="fz30 mgtb10">资金监管</p><p class="fz14">交易资金严格履行资金监管</p></dd>
        </dl>
        <dl class="lf clear-fix">
            <dt class="lf mgr20"><img class="w59" src="<?= ASSETS_BASE_URI ?>ctcf/images/t003@2x.png" alt=""></dt>
            <dd class="lf fz-black"><p class="fz30 mgtb10">全面保障</p><p class="fz14">项目严格筛选多重保障</p></dd>
        </dl>
    </div>
    <!--平台热门推荐-->
    <?php if ($loans) { ?>
    <div class="recommend mgt20">
        <ul class="clear-fix">
            <li class="lf first mgr17">
                <p class="fz30">热门推荐</p>
                <i></i>
                <p class="fz14">热门推荐·精选理财</p>
            </li>
            <?php foreach ($loans as $val) : ?>
            <li class="lf mgr17 special">
                <a href="/deal/deal/detail?sn=<?= $val->sn ?>" target="_blank">
                    <div class="title fz18 fz-gray"><?= $val->title ?></div>
                    <div class="rate fz-orange-strong"><span class="fz36"><?= LoanHelper::getDealRate($val) ?><em class="fz18">%</em><?php if (!empty($val->jiaxi)) { ?><i class="fz18">+<?= doubleval($val->jiaxi) ?>%</i><?php } ?></span></div>
                    <div class="rate-des fz14 fz-gray">预期年化收益率</div>
                    <div class="clear-fix des">
                        <dl class="lf">
                            <dt class="fz16 fz-black"><?php $ex = $val->getDuration() ?><?= $ex['value']?><span> <?= $ex['unit']?></span></dt>
                            <dd class="f14 fz-gray">投资期限</dd>
                        </dl>
                        <dl class="lf">
                            <dt class="fz16 fz-black"><?= StringUtils::amountFormat2($val->start_money) ?>元</dt>
                            <dd class="f14 fz-gray">起投金额</dd>
                        </dl>
                    </div>
                    <div class="progress">
                        <span><i style="width: <?= $val->getProgressForDisplay() ?>%;"></i></span>&nbsp;<?= $val->getProgressForDisplay() ?>%
                    </div>

                    <?php if (OnlineProduct::STATUS_PRE === $val->status) { ?>
                        <span class="btn btn-light-orange f14 fz-white">预告期</span>
                    <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                        <span class="btn btn-orange f14 fz-white">立即投资</span>
                    <?php } elseif (OnlineProduct::STATUS_FULL === $val->status || OnlineProduct::STATUS_FOUND === $val->status) { ?>
                        <span class="btn btn-gray f14 fz-white">已售罄</span>
                    <?php } elseif (OnlineProduct::STATUS_HUAN === $val->status) { ?>
                        <span class="btn btn-light-orange f14 fz-white">收益中</span>
                    <?php } elseif (OnlineProduct::STATUS_OVER === $val->status) { ?>
                        <span class="btn btn-gray f14 fz-white">已还清</span>
                    <?php } ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php } ?>

    <!--定期理财-->
    <?php if ($loans) { ?>
    <div class="financing mgt20">
        <ul class="clear-fix">
            <li class="lf first">
                <p class="fz30">定期理财</p>
                <i></i>
                <p class="fz14">期限灵活·优质资产</p>
            </li>
            <li class="lf  last clear-fix">
                <div class="show-more clear-fix">
                    <div class="lf"></div>
                    <a class="rg fz14 header-hover" href="">更多>></a>
                </div>
                <div class="finance-list">
                    <table>
                        <tr class="fz16 fz-gray" height="69">
                            <td>产品名称</td>
                            <td>预期年化利率</td>
                            <td class="special">期限</td>
                            <td>起投金额</td>
                            <td>募集进度</td>
                        </tr>
                    </table>
                </div>
            </li>
        </ul>
    </div>
    <?php } ?>

    <!--信息公告-->
    <div class="information clear-fix mgt20">
        <div class="lf mgr17">
            <p>
                <span class="lf fz-black fz16">
                    <img class="mgr12 w17" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_05.png" alt="">帮助中心
                </span>
                <a  class="rg fz14 header-hover" href="/helpcenter/operation/" target="_blank">更多>></a>
            </p>
            <ul>
                <li>
                    <a class="clear-fix" href="/helpcenter/operation/" target="_blank">
                        <span class="lf fz14 fz-gray-strong w250 overflow">
                            <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                            网站流程如何操作？
                        </span>
                        <span class="rg header-hover">></span>
                    </a>
                </li>
                <li>
                    <a class="clear-fix" href="/helpcenter/security/" target="_blank">
                        <span class="lf fz14 fz-gray-strong w250 overflow">
                            <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                            为什么在楚天财富投资是安全的？
                        </span>
                        <span class="rg header-hover">></span>
                    </a>
                </li>
                <li>
                    <a class="clear-fix" href="/helpcenter/background/" target="_blank">
                        <span class="lf fz14 fz-gray-strong w250 overflow">
                            <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                            了解楚天财富。
                        </span>
                        <span class="rg header-hover">></span>
                    </a>
                </li>
                <li>
                    <a class="clear-fix" href="/helpcenter/product/" target="_blank">
                        <span class="lf fz14 fz-gray-strong w250 overflow">
                            <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                            资产品种都有哪些？特点和优势是什么？
                        </span>
                        <span class="rg header-hover">></span>
                    </a>
                </li>
                <li>
                    <a class="clear-fix" href="/helpcenter/contact/" target="_blank">
                        <span class="lf fz14 fz-gray-strong w250 overflow">
                            <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                            如何联系我们？
                        </span>
                        <span class="rg header-hover">></span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="lf mgr17">
            <p>
                <span class="lf fz-black fz16">
                    <img class="mgr12 w19" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_06.png" alt="">平台动态
                </span>
                <a  class="rg fz14 header-hover" href="/news/index?type=info" target="_blank">更多>></a>
            </p>
            <ul>
                <?php foreach ($news as $val) : ?>
                    <li>
                        <a class="clear-fix" href="/news/detail?type=info&id=<?= $val->id ?>" target="_blank">
                            <span class="lf fz14 fz-gray-strong w250 overflow">
                                <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                                <?= $val->title ?>
                            </span>
                            <span class="rg header-hover">></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="lf">
            <p>
                <span class="lf fz-black fz16">
                    <img class="mgr12 w15" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_07.png" alt="">理财公告
                </span>
                <a  class="rg fz14 header-hover" href="/news/index?type=notice" target="_blank">更多>></a>
            </p>
            <ul>
                <?php foreach ($notice as $val) : ?>
                    <li>
                        <a class="clear-fix" href="/news/detail?type=notice&id=<?= $val->id ?>" target="_blank">
                            <span class="lf fz14 fz-gray-strong w250 overflow">
                                <img class="mgr12 w4" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_08.png" alt="">
                                <?= $val->title ?>
                            </span>
                            <span class="rg header-hover">></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!--侧边工具栏-->
    <div class="tool-box">
        <div class="tool-bar">
            <a href="javascript:void(0);" class="app">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/weixin@2x.png" alt="">
            </a>
            <a href="javascript:void(0);" class="weiChat">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/weixin@2x.png" alt="">
            </a>
            <a href="javascript:void(0);" class="weibo">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/weibo@2x.png" alt="">
            </a>
        </div>
    </div>
</div>
<script id="entry-template" type="text/x-handlebars-template">
    {{#each data}}
    <tr onclick="window.open('{{link}}');" class="fz14 fz-black">
        <td><div class="overflow">{{title}}</div></td>
        <td class="fz-orange-strong"><span class="fz30">{{dealRate}}<em class="fz18">%</em><i class="fz14">{{#if jiaxi}}+{{jiaxi}}%{{/if}}</i></span></td>
        <td>{{duration}}</td>
        <td>{{startMoney}}元</td>
        <td>
            <div class="progress">
                <span><i style="width:{{progress}}%;"></i></span>&nbsp;{{progress}}%
            </div>
        </td>
        {{#status1 status}}
        <td><div class="btn btn-light-orange f14 fz-white">预告期</div></td>
        {{/status1}}
        {{#status2 status}}
        <td><div class="btn btn-orange f14 fz-white">立即投资</div></td>
        {{/status2}}
        {{#status3 status}}
        <td><div class="btn btn-gray f14 fz-white">已售罄</div></td>
        {{/status3}}
        {{#status5 status}}
        <td><div class="btn btn-light-orange f14 fz-white">收益中</div></td>
        {{/status5}}
        {{#status6 status}}
        <td><div class="btn btn-gray f14 fz-white">已还清</div></td>
        {{/status6}}
    </tr>
    {{/each}}
</script>
<script>
    Handlebars.registerHelper("status1", function (status,options) {
        if (status == 1) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    Handlebars.registerHelper("status2", function (status,options) {
        if (status == 2) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    Handlebars.registerHelper("status3", function (status,options) {
        if (status == 3 || status == 7) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    Handlebars.registerHelper("status5", function (status,options) {
        if (status == 5) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    Handlebars.registerHelper("status6", function (status,options) {
        if (status == 6) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    /*渲染列表开始*/
    $.ajax({
        type: "GET",
        url: "/site/fixed",
        success: function(data){
            if (data.code == 0) {
                var source   = document.getElementById("entry-template").innerHTML;
                var template = Handlebars.compile(source);
                var context  = {
                    data:data.data
                };
                var html = template(context);
                Handlebars.registerHelper("jiaxi", function (jiaxi,options) {
                    if (!jiaxi) {
                        return options.fn(this);
                    } else {
                        return options.inverse(this);
                    }
                });
                $(".finance-list table tbody").append(html);
            } else {
                var html = "<tr><td class='fz18' colspan='5' height='300'>"+data.message+"</td></tr>";
                $(".finance-list table tbody").append(html);
            }
        }
    });
    /*渲染列表结束*/

    $(function(){
        $(window).scroll(function(){
            if($(this).scrollTop()>100){
                $(".tool-box").fadeIn();
            } else {
                $(".tool-box").fadeOut();
            }
        });
        jQuery(".banner").slide({mainCell:".bd ul",effect:"fold",autoPlay:true,trigger:"click",delayTime:1000,interTime:5000});

        jQuery(".txtScroll-top").slide({titCell:".hd ul",mainCell:".bd ul",autoPage:true,effect:"topLoop",autoPlay:true,pnLoop:false});
    })
</script>

<input type="hidden" name="isLoggedin" value="true">
<input type="hidden" name="isInvest" value="true">
<!--已登陆未投资-->
<div class="mask-no-invest">
    <div class="popup-box">
        <div class="popup-box-top"></div>
        <i class="close-box"></i>
        <p class="popup-box-msg"><span>888元</span>红包已经发放到账户中心，请至”账户中心-优惠券”中查看</p>
        <div class="red-racket">
            <span>18元</span>
            <span>20元</span>
            <span>30元</span>
            <span>80元</span>
            <span>150元</span>
            <span>220元</span>
            <span>380元</span>
        </div>
        <p class="popup-box-btn">查看红包</p>
    </div>
</div>
<!-- 未登陆提示-->
<div class="mask-prize-hint">
    <div class="popup-box">
        <div class="popup-box-top"></div>
        <i class="close-box"></i>
        <div class="prize_user_box">
            <a class="prize_user_new clearfix" href="#">
                <img class="lf" src="<?= ASSETS_BASE_URI ?>ctcf/images/mask/prize_user_new.png" alt="">
                <div class="rg">
                    <p class="user-login-msg">新用户登录</p>
                    <p class="user-login-prize">送您<span>888元</span>红包</p>
                </div>
            </a>
            <a class="prize_user_old clearfix" href="#">
                <img class="lf" src="<?= ASSETS_BASE_URI ?>ctcf/images/mask/prize_user_old1.png" alt="">
                <div class="rg">
                    <p class="user-login-msg">老用户登录</p>
                    <p class="user-login-prize">送您补偿红包</p>
                </div>
            </a>
        </div>
        <p class="popup-box-btn">点击领取</p>
    </div>
</div>
<!--已登陆已投资-->
<div class="mask-login-invest">
    <div class="popup-box">
        <i class="close-box"></i>
        <div class="urgrade-swiper-contain swiper-no-swiping">
            <ul class="swiper-wrapper clear-fix" id="upgrade-sweper">
                <li class="swiper-slide upgrade_explain">
                    <h5>尊敬的用户，您好：</h5>
                    <p class="login-invest-msg">2018年楚天财富系统全新升级，并建立了全新会员和积分体系。平台根据您的历史投资额，特发放了豪华补偿礼包，敬请收下！</p>
                    <table>
                        <tr>
                            <th>累计年华投资金额（万元）</th>
                            <th>补偿红包</th>
                            <th>补偿积分</th>
                        </tr>
                        <tr>
                            <td>0＜X＜20</td>
                            <td>188元</td>
                            <td>188</td>
                        </tr>
                        <tr>
                            <td>20≤X＜50</td>
                            <td>268元</td>
                            <td>388</td>
                        </tr>
                        <tr>
                            <td>≥50</td>
                            <td>480元</td>
                            <td>588</td>
                        </tr>
                    </table>
                    <a class="check-login-invest">查看我的升级红包</a>
                </li>
                <li class="swiper-slide upgrade-updata">
                    <h5>您获得的升级礼包</h5>
                    <div class="upgrade-updata-contain">
                        <div class="updata-top-part">
                            <span>18元</span>
                            <span>50元</span>
                            <span>120元</span>
                        </div>
                        <div class="updata-mid-part">共得<span>188元</span>红包</div>
                        <div class="updata-bottom-part clearfix">
                            <div class="lf bottom-jf-prize">
                                <i></i>
                            </div>
                            <div class="rg">
                                <p>188积分</p>
                                <p>可以兑换超多礼品</p>
                            </div>
                        </div>
                    </div>
                    <u class="get-prize-rules">补偿规则</u>
                    <div class="upgrade-updata-btn clearfix">
                        <a class="check-get-prize lf" href="#">查看红包</a>
                        <a class="go-to-store rg" href="#">逛逛积分商城</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="swiper-pagination">
            <span class="swiper-span-active"></span>
            <span class="last-pagination-span"></span>
        </div>
    </div>
</div>
