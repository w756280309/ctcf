<?php

use common\utils\StringUtils;
use common\view\LoanHelper;
use wap\assets\WapAsset;
use common\view\UdeskWebIMHelper;
use yii\web\YiiAsset;

$this->title = '购买';

$validCouponCount = count($validCoupons);
UdeskWebIMHelper::init($this);

$yr = $deal->yield_rate;
$qixian = $deal->getDuration()['value'];
$retmet = $deal->refund_method;
$sn = $deal->sn;
$isFlexRate = $deal->isFlexRate;
$this->registerJs(<<<JS
    var yr = "$yr";
    var qixian = "$qixian";
    var retmet = "$retmet";
    var sn = "$sn";
    var isFlexRate = Boolean("$isFlexRate");
    var validCouponCount = $validCouponCount;
JS
    , 1);

$this->registerJsFile(ASSETS_BASE_URI . 'js/order.js?v=20171108', ['depends' => YiiAsset::class]);

$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/buy-setting/setting.css?v=20180211', ['depends' => WapAsset::class]);


?>
    <link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/css/index.css?v=1">
    <script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
    <script src="<?= FE_BASE_URI ?>/libs/fastclick.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/common.js?v=1"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/layer.js?v=1"></script>

    <style>
	    .produce{
		    background-color: #ff6058;
	    }
	    .produce .col-xs-4{
		    padding-left: 0.61333rem;
		    padding-right: 0;
		    color: #ffd8d6;
	    }
	    .login-sign-btn{
		    margin-top:0.37333333rem;
		    height:auto;
	    }
	    .login-sign-btn #buybtn{
		    width: 92%;
		    height:1.06666667rem;
		    line-height:1.06666667rem;
		    background-color: #ff6058;
		    color: #fff;
		    border-radius: 4px;
	    }
	    .pop-limitCoupon{
		    position: relative;
		    top:-0.32rem;
		    width: 100%;
		    height:1.38666667rem;
		    line-height:1.7rem;
		    background: url("<?= ASSETS_BASE_URI ?>images/bg_pop_limit.png") no-repeat;
		    background-size: 100%;
		    text-align: center;
		    font-size: 12px;
		    color: #c9944e;
		    z-index: 100;
	    }
    </style>
    <!--   购买页 start-->
    <div class="row produce">
        <div class="col-xs-12 text-align-lf first-hang" style="padding-left: 0.613rem;"><?= $deal->title ?></div>
        <div style="width: 35%;" class="col-xs-4 text-align-lf">预期年化收益</div>
        <div style="width: 65%;" class="col-xs-8 text-align-lf col"><?= LoanHelper::getDealRate($deal) ?>
            %<?php if (!empty($deal->jiaxi)) { ?>+<?= $deal->jiaxi ?>%<?php } ?></div>
        <div style="width: 35%;" class="col-xs-4 text-align-lf">项目期限</div>
        <div style="width: 65%;" class="col-xs-8 text-align-lf col">
            <?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><?= $ex['unit'] ?>
        </div>
        <div style="width: 35%;" class="col-xs-4 text-align-lf">可投余额</div>
        <div style="width: 65%;" class="col-xs-8 text-align-lf col"><?= StringUtils::amountFormat3($deal->getLoanBalance()) ?>元</div>
        <div style="width: 35%;" class="col-xs-4 text-align-lf">起投金额</div>
        <div style="width: 65%;" class="col-xs-8 text-align-lf col"><?= StringUtils::amountFormat3($deal->start_money) ?>元</div>
        <div style="width: 35%;" class="col-xs-4 text-align-lf">递增金额</div>
        <div style="width: 65%;" class="col-xs-8 text-align-lf col"><?= StringUtils::amountFormat3($deal->dizeng_money) ?>元</div>
    </div>
    <div class="row surplus margin-top">
        <div class="lf" style="width: 31%;color:#aab2bd;">可用余额</div>
        <div class="lf" style="width: 51%;color: #434a54;"><?= StringUtils::amountFormat3($user->lendAccount->available_balance) ?>
            元
        </div>
        <div class="lf" style="width: 14%;" onclick="location.href('/user/userbank/recharge')"><a
                    href="/user/userbank/recharge?from=<?= urlencode('/order/order?sn=' . $deal->sn) ?>">去充值</a></div>
    </div>
    <form style="position: relative;" action="/order/order/doorder?sn=<?= $deal->sn ?>" method="post" id="orderform" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row surplus">
            <div class="lf" style="border:none;width: 31%;color:#a3a4a6;">投资金额(元)</div>
            <input name="money" maxlength="8" type="tel" id="money" value="<?= empty($money) ? '' : $money ?>"
                   placeholder="请输入投资金额" class="lf" step="any" autocomplete="off">
            <div id="btnDelete" class="col-xs-2 safe-txt" style="position: absolute;right: 0;border:none;width: 14%;"><img class="delete-input" src="<?= ASSETS_BASE_URI ?>images/icon_delete.png" alt=""></div>
        </div>
            <input name="couponConfirm" id="couponConfirm" type="text" value="" hidden="hidden">
            <div class="row sm-height" id="coupon">
                <?php if ($deal->allowUseCoupon === 1 || $deal->allowRateCoupon === 1) { ?>
                    <?= $this->renderFile('@app/modules/user/views/coupon/_valid_coupon.php', ['coupons' => $coupons]) ?>
                <?php } else { ?>
                    <div class="lf" style="width: 31%;font-size: 14px;color:#a3a4a6;">优惠券</div>
                    <div class="safe-txt">此标的代金券和加息券都不可用</div>
                <?php } ?>
            </div>

        <?php if ($deal->allowUseCoupon === 0 && $deal->allowRateCoupon === 1) {  ?>
            <div class="pop-limitCoupon">此标的不可使用代金券</div>
        <?php } elseif ($deal->allowRateCoupon === 0 && $deal->allowUseCoupon === 1) { ?>
            <div class="pop-limitCoupon">此标的不可使用加息券</div>
        <?php } ?>

        <div class="row shouyi">
            <div class="lf" style="font-size:13px;border-bottom: 1px solid #f0f0f0;width: 31%;color:#a3a4a6;">实际支付</div>
            <div class="lf shijizhifu" style="border-bottom: 1px solid #f0f0f0;width: 65%;color: #4e545a">0.00元</div>
        </div>
        <div class="row shouyi">
            <div class="lf" style="width: 31%;color:#a3a4a6;font-size: 13px">预计收益</div>
            <div class="lf yuqishouyi" style="width: 65%;color: #4e545a">0.00元</div>
        </div>

        <div class="row login-sign-btn ht">
	          <input id="buybtn" class="btn-common btn-normal" type="submit" value="购买">
        </div>
        <div class="row" style="margin: 0.533rem auto .5rem;">
            <center><a style="text-align: left;padding-left: 0.613rem;" class="col-xs-12 lf" href="/order/order/agreement?id=<?= $deal->id ?>">我已阅读并同意<span style="color: #419bf9">《产品合同》</span></a></center>
        </div>
    </form>

    <div id="info" class="bing-info">
        <div class="bing-tishi">提示</div>
        <p></p>
        <div class="bind-btn">
            <span class="bind-xian x-cancel">取消</span>
            <span class="x-confirm">确定</span>
        </div>
    </div>
<p id="btn_udesk_im" style="color:#434a54;margin-bottom: 1rem;"><img src="<?= FE_BASE_URI ?>wap/new-homepage/images/online-service-blue.png">在线客服</p>
    <div class="mask2" style="display: none"></div>
    <div class="coupon-box" style="display: none">
        <div class="coupon-box-title clearfix">
            <a class="rules-details">详细规则</a>
            <span>只选择一种优惠券使用</span>
            <div class="btn-close"></div>
        </div>
        <div class="coupon-box-kinds">
            <ul class="coupon-kinds clearfix">
                <li class="daijin lf">
                    <div class="daijin-a " style="display: none">
                        <img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_chit.png" alt="">
                        <span style="color:#9ea0a4;">代金券</span>
                    </div>
                    <div class="daijin-b" style="border-bottom:2px solid #6d96e0;">
                        <img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_chit_a.png" alt="">
                        <span style="color: #6d96e0">代金券</span>
                    </div>
                </li>
                <li class="jiaxi rg">
                    <div class="jiaxi-a ">
                        <img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_rate.png" alt="">
                        <span style="color:#9ea0a4 ">加息券</span>
                    </div>
                    <div class="jiaxi-b " style="display: none;border-bottom: 2px solid #fc9153;">
                        <img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_rate_a.png" alt="">
                        <span style="color:#fc9153 ">加息券</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="coupon-box-content ">
            <div class="daijin-content scroll-slide">
                <!--				<div id="wrapper">-->
                <ul class="daijin-list ">
                    <p class="daijin-remind">您可以选择多张代金券</p>
                </ul>
                <!--				</div>-->
                <div class="daijin-sure" style="position: fixed;height: 1.4rem;padding-top: 0.21rem;">
                    <p class="line-one">确认选择</p>
                    <p class="line-two">(您可以选择多张代金券)</p>
                </div>
                <div class="daijin-none" style="display: none">
                    <p class="daijin-none-title">暂无可用代金券</p>
                    <p class="daijin-none-content">参与平台活动和关注平台动态</p>
                    <p class="daijin-none-content">将有机会获得代金券哦！</p>
                    <div class="daijin-none-link">
                        <p><img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_phone.png" alt="">客服电话：<?= Yii::$app->params['platform_info.contact_tel'] ?>
                        </p>
                        <p>(9:00-20:00)</p>
                    </div>
                </div>
            </div>
            <div class="jiaxi-content scroll-slide" style="display: none;">
                <!--				<div id="wrapper2">-->
                <ul class="jiaxi-list "></ul>
                <!--				</div>-->
                <div class="jiaxi-remind" style="position: fixed;bottom: 0;background-color: #fff;">
                    <div class="jiaxi-remind-title"><span></span>温馨提示<span style="left:6.26rem;"></span></div>
                    <div class="jiaxi-remind-content">加息收益将随最后一期本息一起回到您的账户，项目中途转让将不享受任何加息券带来的收益。</div>
                </div>
                <div class="jiaxi-none" style="display: none;">
                    <p class="daijin-none-title">暂无可用加息券</p>
                    <p class="daijin-none-content">参与平台活动和关注平台动态</p>
                    <p class="daijin-none-content">将有机会获得加息券哦！</p>
                    <div class="daijin-none-link">
                        <p><img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_phone.png" alt="">客服电话：<?= Yii::$app->params['platform_info.contact_tel'] ?>
                        </p>
                        <p>(9:00-20:00)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="rules-box" style="display: none">
        <p class="rules-title"><img src="<?= FE_BASE_URI ?>wap/rate-coupon/bid-page/images/icon_close.png" alt="">优惠券规则
        </p>
        <p class="rules-parts">规则概述</p>
        <ul class="rules-content">
            <li>理财卡券只适用于部分标的，其中转让标不可使用，同一项目代金券和加息券不可叠加使用。</li>
        </ul>
        <p class="rules-parts">代金券</p>
        <ul class="rules-content">
            <li>一次投资可使用多张代金券，多张代金券使用需要满足投资额不少于多张代金券的累计金额。</li>
            <li>代金券不能与加息券叠加使用。</li>
            <li>代金券使用后，所投资项目开始收益后，代金券金额将成为账户总资产的一部分。</li>
        </ul>
        <p class="rules-parts">加息券</p>
        <ul class="rules-content">
            <li>一次投资使用1张加息券，使用后项目在加息时间内获得额外年化收益。</li>
            <li>加息券不可叠加使用，也不与其他卡券叠加使用。</li>
            <li>通过加息券获得的收益将随项目最后一期本息一期回到您的账户；项目中途转让将不享受任何加息券带来的收益。</li>
        </ul>
        <p class="rules-phone">详情咨询：400-101-5151</p>
    </div>
    <input type="hidden" id="selectedCouponAmount" value="0">
    <input type="hidden" id="selectedCouponCount" value="0">
    <input type="hidden" id="selectedCouponRate" value="0">
    <input type="hidden" id="selectedCouponRateday" value="0">
<?php if ($deal->allowUseCoupon ===1 || $deal->allowRateCoupon === 1) { //修改T857问题,此处修改为邹键双找到的问题点?>
    <script type="text/javascript">
        FastClick.attach(document.body);
        var money = $('#money').val();
        var sn = GetQueryString("sn");

        function delcommafy(num) {
            if ((num + "").trim() == "") {
                return "";
            }
            num = num.replace(/,/gi, '');
            return num;
        }

        function initCouponPage() {
            money = $('#money').val();
            if (!money) {
                money = 0
            }
            $.ajax({
                url: '/user/coupon/available-coupons',
                type: 'get',
                data: {money: money, sn: sn},
                dataType: 'json',
                async: false,
                success: function (data) {
                    if (data.code == 1) {
                        $(".daijin-list li,.jiaxi-list li").remove();
                        //渲染优惠券
                        for (var i = 0; i < data.CouponList.length; i++) {
                            var limitTxt = "";
                            var limitTxt2 = "";
                            if (data.CouponList[i].loanCategories == 3) {
                                limitTxt = "仅用于新手标产品";
                            } else if (data.CouponList[i].loanCategories == 2) {
                                limitTxt = " 温盈恒产品可用";
                            } else if (data.CouponList[i].loanCategories == 1) {
                                limitTxt = "温盈金产品可用";
                            } else {
                                limitTxt = "正式标产品可用";
                            }
                            if (!!data.CouponList[i].loanExpires) {
                                limitTxt2 = "投资满" + parseInt(data.CouponList[i].minInvest) + "元可用"
                            }
                            var jineTxt;
                            if (Number(data.CouponList[i].minInvest) < 10000) {
                                jineTxt = "满" + Number(data.CouponList[i].minInvest) + "元可用";
                            } else {
                                jineTxt = "满" + Number(data.CouponList[i].minInvest) / 10000 + "万元可用";
                            }
                            if (data.CouponList[i].type == 0) {
                                //代金券
                                $(".daijin-list").append(
                                    '<li class="clearfix" data-couponid="' + data.CouponList[i].userCouponId + '">' +
                                    '<div class="num-part">' +
                                    '<p class="number">￥<span>' + data.CouponList[i].amount + '</span></p>' +
                                    '<p class="limit">' + jineTxt + '</p>' +
                                    '</div>' +
                                    '<div class="word-part">' +
                                    '<p class="coupon-title">' + data.CouponList[i].name + '</p>' +
                                    '<p class="coupon-date">有效期至' + data.CouponList[i].expiryDate +
                                    '<p class="coupon-details">' + limitTxt + '<br>' + limitTxt2 + '</p>' +
                                    '</div>' +
                                    '</li>'
                                )
                            } else {
                                //加息券
                                $(".jiaxi-list").append(
                                    '<li class="clearfix" data-couponid="' + data.CouponList[i].userCouponId + '">' +
                                    '<div class="num-part">' +
                                    '<p class="number">+<span>' + Number(data.CouponList[i].bonusRate).toFixed(1) + '%</span></p>' +
                                    '<p class="limit">加息<span>' + data.CouponList[i].bonusDays + '</span>天</p>' +
                                    '</div>' +
                                    '<div class="word-part">' +
                                    '<p class="coupon-title">' + data.CouponList[i].name + '</p>' +
                                    '<p class="coupon-date">有效期至' + data.CouponList[i].expiryDate + '</p>' +
                                    '<p class="coupon-details">' + limitTxt + '<br>' + limitTxt2 + '</p>' +
                                    '</div>' +
                                    '</li>'
                                );
                            }
                        }
                    }
                    if (data.selected.couponId.length > 0) {
                        for (var i = 0; i < data.selected.couponId.length; i++) {
                            $(".coupon-box li").each(function () {
                                if ($(this).data("couponid") == data.selected.couponId[i]) {
                                    $(this).addClass("active");
                                }
                            })
                        }
                    }
                    if (data.total.type === 1) {
                        $(".daijin-remind").html("您可以选多张代金券");
                        $(".line-two").html("(您可以选多张代金券)");
                        var jiaxiNum = parseInt(data.total.sum);
                        var jiaxiDay = data.total.count;
                        $(".safe-txt .notice").html("赠" + jiaxiNum + "%加息 收益" + jiaxiDay + "天");
                        $("#selectedCouponAmount").val(0);
                        $("#selectedCouponCount").val(1);
                        $("#selectedCouponRate").val(jiaxiNum);
                        $("#selectedCouponRateday").val(jiaxiDay);
                    } else if (data.total.type === 0) {
                        var daijinNum = parseInt(data.total.sum);
                        var daijinCount = data.total.count;
                        $(".daijin-remind").html("已选<span style='color:#6d96e0'>" + daijinCount + "</span>张代金券，可抵扣<span style='color:#6d96e0'>" + daijinNum + "</span>元投资")
                        $(".line-two").html("(已选" + daijinCount + "张代金券，可抵扣" + daijinNum + "元投资)");
                        $(".safe-txt .notice").html("抵扣" + daijinNum + "元(" + daijinCount + "张代金券)")
                        $("#selectedCouponAmount").val(daijinNum);
                        $("#selectedCouponCount").val(daijinCount);
                    }
                }
            });
            if ($(".daijin-list").find("li").length == 0) {
                $(".daijin-list,.daijin-sure").hide();
                $(".daijin-none").show();
            } else {
                $(".daijin-list,.daijin-sure").show();
                $(".daijin-none").hide();
                if ($(".daijin-list").find("li").length < 5) {
                    $(".daijin-list").css("height", "71vh")
                }
            }
            if ($(".jiaxi-list").find("li").length == 0) {
                $(".jiaxi-list,.jiaxi-remind").hide();
                $(".jiaxi-none").show();
            } else {
                $(".jiaxi-list,.jiaxi-remind").show();
                $(".jiaxi-none").hide();
                if ($(".jiaxi-list").find("li").length < 4) {
                    $(".jiaxi-list").css("height", "67vh")
                }
            }
        }

        function toCoupon() {
            var money = $('#money').val();
            if (!money) {
                toastCenter("请先输入投资金额");
            } else {
                $(".produce,.surplus,#orderform,#btn_udesk_im").hide();
                $(".coupon-box").show();
            }
        }

        function getValidCoupon(money) {
            var xhr = $.get('/user/coupon/valid-for-loan?sn=<?= $deal->sn ?>&money=' + money, function (data) {
                $('#coupon').html(data);
                profit($('#money'));
            });
        }

        function GetQueryString(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]);
            return null;
        }

        $(function () {
            forceReload_V2();
            $("#btnDelete").on("click",function () {
	            $("input[name]").val("");
              profit($("input[name]"));
                $(".safe-txt .notice").html("选择代金券或加息券");
            });
            $('#money').on('keyup', function () {
                money = $(this).val();
                $("#selectedCouponAmount").val(0);
                $("#selectedCouponCount").val(0);
                $("#selectedCouponRate").val(0);
                $("#selectedCouponRateday").val(0);
                profit($('#money'));
                initCouponPage();
                $(".safe-txt .notice").html("选择代金券或加息券");
            });
            money = 0;
            initCouponPage();
            //点击蒙层关闭优惠券页
            $(".coupon-box-title .btn-close").click(function () {
                $(".coupon-box").hide();
                $(".produce,.surplus,#orderform,#btn_udesk_im").show();
            });
            //优惠券table选项卡
            $(".coupon-kinds li").click(function () {
                if ($(this).index() == 0) {
                    $(".daijin-content").show();
                    $(".jiaxi-content").hide();
                } else {
                    $(".daijin-content").hide();
                    $(".jiaxi-content").show();
                }
                $(this).children().eq(1).show();
                $(this).children().eq(0).hide();
                $(this).siblings().children().eq(0).show();
                $(this).siblings().children().eq(1).hide();
            });
            //选择加息券
            $("body").on("click", ".jiaxi-list li", function () {
                var couponId = $(this).data("couponid");
                var $this = $(this);
                if ($(this).hasClass("active")) {

                } else {
                    $.ajax({
                        url: "/user/coupon/add-coupon",
                        data: {
                            sn: sn,
                            couponId: couponId,
                            money: money,
                            opt: 'selected'
                        },
                        type: "get",
                        async: false,
                        success: function (data) {
                            var couponList = data.data.coupons;
                            $(".coupon-box-content ul li").removeClass("active");
                            for (var i = 0; i < couponList.length; i++) {
                                $(".coupon-box-content ul li").each(function () {
                                    if ($(this).data("couponid") == couponList[i]) {
                                        $(this).addClass("active");
                                    }
                                })
                            }
                            $(".daijin-remind").html("您可以选多张代金券");
                            $(".line-two").html("(您可以选多张代金券)");
                            var jiaxiNum = $this.find(".num-part .number span").text();
                            var jiaxiDay = Number($this.find(".num-part .limit span").text());
                            $(".safe-txt .notice").html("赠" + jiaxiNum + "加息 收益" + jiaxiDay + "天");
                            $("#selectedCouponAmount").val(0);
                            $("#selectedCouponCount").val(1);
                            $("#selectedCouponRate").val(jiaxiNum);
                            $("#selectedCouponRateday").val(jiaxiDay);
                            $(".coupon-box").hide();
                            $(".produce,.surplus,#orderform,#btn_udesk_im").show();
                            if (data.data.jiaxi > 0 && data.data.yjsy > 0) {
                                $('.yuqishouyi').html(data.data.yjsy + "元(含加息"+ data.data.jiaxi +"元)");
                                $('.shijizhifu').html(money + '.00元');
                            }

                            //profit($('#money'));
//                            var profitNow = delcommafy($('.yuqishouyi').text().slice(0, -1));
//                            var profitNew = Number(profitNow) + Number(data.data.jiaxi);
//                            $('.yuqishouyi').html(profitNew.toFixed(2) + "元(含加息" + data.data.jiaxi + "元)");
                        },
                        error: function (jqXHR) {
                            toastCenter(jqXHR.responseJSON.message)
                        }
                    });
                }
            });
            //选择代金券
            $("body").on("click", ".daijin-list li", function () {

                var couponId = $(this).data("couponid");
                var opt;
                var $this = $(this);

                if ($(this).hasClass("active")) {
                    opt = "canceled";
                } else {
                    opt = "selected";
                }
                $.ajax({
                    url: "/user/coupon/add-coupon",
                    data: {
                        sn: sn,
                        couponId: couponId,
                        money: money,
                        opt: opt
                    },
                    type: "get",
                    async: false,
                    success: function (data) {
                        if (data.code == 0) {
                            $(".coupon-box-content ul li").removeClass("active");
                            var couponList = data.data.coupons;
                            for (var i = 0; i < couponList.length; i++) {
                                $(".coupon-box-content ul li").each(function () {
                                    if ($(this).data("couponid") == couponList[i]) {
                                        $(this).addClass("active");
                                    }
                                })
                            }
                            if (data.data.coupons.length > 0) {
                                var daijinNum = data.data.money;
                                var daijinCount = data.data.total;
                                $(".daijin-remind").html("已选<span style='color:#6d96e0'>" + daijinCount + "</span>张代金券，可抵扣<span style='color:#6d96e0'>" + daijinNum + "</span>元投资")
                                $(".line-two").html("(已选" + daijinCount + "张代金券，可抵扣" + daijinNum + "元投资)");
                                $(".safe-txt .notice").html("抵扣" + daijinNum + "元(" + daijinCount + "张代金券)");
                                $("#selectedCouponAmount").val(daijinNum);
                                $("#selectedCouponCount").val(daijinCount);
                            } else {
                                $(".daijin-remind").html("您可以选择多张代金券");
                                $(".line-two").html("您可以选择多张代金券");
                                $(".safe-txt .notice").html("选择代金券或加息券");
                                $("#selectedCouponAmount").val(0);
                                $("#selectedCouponCount").val(0);
                            }
                            profit($('#money'));
                        }
                    },
                    error: function (jqXHR) {
                        toastCenter(jqXHR.responseJSON.message)
                    }
                });
            });
            //关闭规则
            $(".rules-title img").click(function () {
                $(".rules-box").hide();
            });
            //显示规则
            $(".rules-details").click(function () {
                $(".rules-box").show();
            });
            //确认代金券按钮
            $(".daijin-sure").click(function () {
                $(".coupon-box").hide();
                $(".produce,.surplus,#orderform,#btn_udesk_im").show();

            });
        })
    </script>
<?php } else { ?>
    <script type="text/javascript">
        $(function () {
            $('#money').on('keyup', function () {
                profit($(this));
            });
        })
    </script>
<?php } ?>