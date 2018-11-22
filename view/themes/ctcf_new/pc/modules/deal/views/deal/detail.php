<?php

use common\models\product\RateSteps;
use common\models\product\OnlineProduct;
use common\models\product\RedeemHelper;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\HtmlPurifier;
use yii\web\JqueryAsset;

$this->title = '项目详情';

$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/detail.js?v=1802231111111111');
$this->registerCssFile(ASSETS_BASE_URI.'css/deal/buy.css');
$this->registerCssFile(ASSETS_BASE_URI.'css/deal/deallist.css?v=161124');
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/deal/detail.css?v=1704140102');
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css');
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/chargedeposit.css');
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.ba-throttle-debounce.min.js?v=161008', ['depends' => JqueryAsset::class]);

?>
<div class="project-box clearfix">
    <div class="project-left clearfix">
        <!--pL-top-box-->
        <div class="pL-top-box">
            <div class="pl-top-title"><?= $deal->title ?></div>
            <div class="pl-middle">
                <ul class="clearfix">
                    <li class="yield_rate">
                        <div class="clearfix">
                            <span class="pl-middle-inner">
                                <?= LoanHelper::getDealRate($deal) ?><i>%</i>
                                <em class="pl-m-add">
                                    <?php if (!empty($deal->jiaxi)) { ?>+<?= StringUtils::amountFormat2($deal->jiaxi) ?>%<?php } ?>
                                </em>
                            </span>
                            <p>借贷双方约定利率</p>
                        </div>
                    </li>
                    <li>
                        <div class="expires">
                            <span class="pl-middle-inner">
                                <?php $ex = $deal->getDuration(); ?><?= $ex['value']?><i><?= $ex['unit'] ?></i>
                            </span>
                            <p>借款期限</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="pl-bottom">
                <ul>
                    <li class="amount">
                        借款金额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat1('{amount}{unit}', $deal->money) ?></span>
                    </li>
                    <li>
                        还款方式:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></span>
                        <?php if ($deal->isNatureRefundMethod()) { ?>
                            <img id="tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                            <div class="dialog">付息时间固定日期，按自然月在每个月还款，按自然季度是3、6、9、12月还款，按自然半年是6、12月还款，按自然年是12月还款</div>
                        <?php } ?>
                    </li>
                    <li class="amount">
                        标的计息日:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $deal->jixi_time > 0 ? date('Y-m-d', $deal->jixi_time) : '项目成立日次日'; ?></span>
                    </li>
                    <?php if (0 === (int) $deal->finish_date) { ?>
                        <li>
                            借款期限:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span>
                                <?php $ex = $deal->getDuration(); ?><?= $ex['value']?><?= $ex['unit'] ?>
                            </span>
                        </li>
                    <?php } else { ?>
                        <li>产品到期日:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= date('Y-m-d', $deal->finish_date) ?></span></li>
                    <?php } ?>
                    <li class="amount">
                        起借金额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat2($deal->start_money) ?>元</span>
                    </li>
                    <li>
                        递增金额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat2($deal->dizeng_money) ?>元</span>
                    </li>
                </ul>
            </div>

            <?php
                $graceDaysDescription = LoanHelper::getGraceDaysDescription($deal);
            ?>
            <?php if (!empty($graceDaysDescription)) { ?>
                <p class="grace-period"><?= $graceDaysDescription?></p>
            <?php } ?>

            <?php if ($deal->is_xs) { ?>
                <p class="grace-period notice-coupon">新手专享标每账户限出借一次，限购一万元。</p>
            <?php } ?>

            <?php if (!$deal->allowUseCoupon) { ?>
                <p class="grace-period notice-coupon">此标的不参与活动，不可使用代金券。</p>
            <?php } ?>
        </div>
        <?php if ($deal->isFlexRate && !$deal->isRedeemable) { ?>
            <!--pl-subscription-->
            <div class="pl-subscription">
                <ul class="clearfix">
                    <li class="pl-grayBg"><span>出借金额（元）</span></li>
                    <li class="pl-grayBg pl-center">预期年化收益率（%）</li>
                    <li>
                        <div><i>累计出借<?= StringUtils::amountFormat2($deal->start_money) ?>起</i></div>
                    </li>
                    <li>
                        <div class="pl-rborder pl-center"><?= StringUtils::amountFormat2($deal->yield_rate * 100) ?></div>
                    </li>
                    <?php foreach (RateSteps::parse($deal->rateSteps) as $val) { ?>
                        <li>
                            <div><i>累计出借<?= StringUtils::amountFormat2($val['min']) ?>起</i></div>
                        </li>
                        <li>
                            <div class="pl-rborder pl-center"><?= StringUtils::amountFormat2($val['rate']) ?></div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>


        <!--        1225添加出借表格和赎回说明开始-->
        <!-- 添加内容开始 -->
        <?php if ($deal->isRedeemable) { ?>
        <div class=earn-ransom>
            <!-- <h5>收益说明</h5> -->
            <table class='earnings-tips'>
                <tr>
                    <th>出借金额（元）</th>
                    <th>提前赎回利率</th>
                    <th>到期兑付利率</th>
                </tr>
                <tr>
                    <td>累计出借10万(含)起</td>
                    <td>8.6</td>
                    <td>9</td>
                </tr>
                <tr>
                    <td>累计出借50万(含)起</td>
                    <td>8.8</td>
                    <td>9.3</td>
                </tr>
                <tr>
                    <td>累计出借100万(含)起</td>
                    <td>9.1</td>
                    <td>9.5</td>
                </tr>
            </table>
            <h5 class='ransom-h5'>赎回说明</h5>
            <ol class="ransom-list" id='ransom-list'>
                <li class="ransom-list-weidth">建议出借者选择到期后自动兑付，最高利率达到9.5%；提前赎回将根据实际出借金额和出借期限计算实际收益，收益率低于到期自动兑付的利率。</li>
                <?php
                    $closedExpireTime = RedeemHelper::getClosedPeriodExpireTime($deal->redemptionPeriods);
                    if (null !== $closedExpireTime) {
                ?>
                    <li class="ransom-list-weidth">封闭期：本产品从购买日至<?= $closedExpireTime->format('Y年m月d日') ?>属封闭期，出借者在封闭期内不得赎回产品。</li>
                <?php } ?>
                <li>提前赎回：封闭期过后，出借者可在<?= RedeemHelper::formatRedemptionPeriods($deal->redemptionPeriods) ?>期间预约提前赎回；预约成功后，<?= RedeemHelper::formatRedemptionPaymentDates($deal->redemptionPaymentDates) ?>当日为出借者兑付所有出借本金及收益。赎回不收取任何手续费。</li>
                <li>到期自动兑付：如出借者不提前赎回，产品从购买日起算，满3年后，自动兑付所有出借本金及收益，收益率高于提前赎回利率。具体收益详见收益说明。</li>
                <li>付息方式说明：购买后，每自然年6月30日，12月30日按照提前赎回利率支付收益。若出借者未申请提前赎回，将在产品到期日补足所有差额收益。</li>
            </ol>
        </div>
        <?php } ?>


        <!-- 添加内容结束 -->
        <!--        1225添加出借表格和赎回说明结束-->


        <!--pl-detail-->
        <div class="pl-detail">
            <div class="plD-title">
                <div class="plD-btn plD-redBorder">
                    <div class="plD-redBorder">借款详情</div>
                </div>
                <div class="plD-btn">
                    <div>出借记录</div>
                </div>
                <!--10 等额本息 JS生成还款计划 -->
                <?php if ($deal->refund_method == '10') { ?>                    
                <div class="plD-btn">
                    <div>还款计划</div>
                </div>     
                <?php } ?>           
            </div>
            <div class="plD-content show">
                <div class="plD-inner-box">
                    <?= HtmlPurifier::process($deal->description) ?>
                </div>
            </div>
            <div class="plD-content hide" id="order_list"></div>
            <!--10 等额本息 JS生成还款计划 -->
            <?php if ($deal->refund_method == '10') { ?>            
            <div class="plD-content hide">
                <div class="piD-data-box">
                    <table class="piD-data-inner">
                        <tbody>
                            <tr style="background: rgb(239, 239, 243);">
                                <th class="piD-th2 piD-left"><span>还款期数</span></th>
                                <th class="piD-th2 piD-left"><span>还款日期</span></th>
                                <th class="piD-th3 piD-right"><span>本期利息</span></th>
                                <th class="piD-th4 piD-right"><span>还款本金</span></th>
                            </tr>
                        <?php
                            $lastbenjin = $deal->money ;
                            $perratio = (LoanHelper::getDealRate($deal) + $deal->jiaxi)/12/100;
                            $pertotal = ($lastbenjin * $perratio * pow((1+$perratio),$ex['value']))/(pow((1+$perratio),$ex['value']) - 1);

                            for ($i = 1;$i <= $ex['value']; $i++) {
                                $thislixi = $lastbenjin*$perratio;
                                $thisbenjin = $pertotal - $thislixi;
                                $lastmonth = $ex['value'] - $i;
                                $thisriqi = $deal->finish_date > 0 ? date('Y-m-d',strtotime("- $lastmonth month",$deal->finish_date)) : ($deal->jixi_time > 0 ? date('Y-m-d',strtotime("+ $i month",$deal->jixi_time)) : "待计息后确定");
                        ?>
                        <tr style="">
                            <td class="piD-th1 piD-left"><span>第<?=$i?>期</span></td>
                            <td class="piD-th2 piD-left"><span><?=$thisriqi?></span></td>
                            <td class="piD-th3 piD-right"><span><?=StringUtils::amountFormat3($thislixi)?></span></td>
                            <td class="piD-th4 piD-right"><span><?=StringUtils::amountFormat3($thisbenjin)?></span></td>
                        </tr>
                        <?php
                                $lastbenjin = $lastbenjin - $thisbenjin;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>
        </div>
    </div>
    <div class="project-right">
        <div class="pR-box clearfix">
            <div class="pR-box-inner">
                <div class="pR-title">借款进度：</div>
                <div class="dR-progress-box">
                    <div class="dR-progress">
                        <span data-progress="<?= $deal->getProgressForDisplay()?>" style="width: <?= $deal->getProgressForDisplay()?>%;"></span>
                    </div>
                    <div class="dRP-data"><?= $deal->getProgressForDisplay()?>%</div>
                </div>
                <ul class="clearfix dR-inner">
                    <li class="dR-inner-left">标的可出借金额：</li>
                    <li class="dR-inner-right">
                        <span><i>
                                <?= StringUtils::amountFormat2($deal->getLoanBalance())?>元
                            </i></span>
                    </li>
                    <li class="dR-inner-left">我的可用余额：</li>
                    <li class="dR-inner-right"><?= (null === $user) ? '查看余额请【<a onclick="login()" style="cursor: pointer">登录</a>】' : ($user->lendAccount ? StringUtils::amountFormat3($user->lendAccount->available_balance).' 元' : '0 元') ?></li>
                    <?php if ($deal->status == OnlineProduct::STATUS_NOW && $deal->end_date >= time()) { ?>
                        <li class="dR-inner-left">出借金额(元)：</li>
                        <li class="dR-inner-right"><a style="cursor: pointer" onclick="chongzhi()">去充值</a></li>
                    <?php } ?>
                </ul>
                <!--已售罄-->
                <?php if ($deal->status == OnlineProduct::STATUS_NOW) { ?>
                    <?php if (null !== $user && $deal->is_xs && $user->xsCount() >= Yii::$app->params['xs_trade_limit']) { ?>
                        <div class="dR-shouqing">您已经参与过新手专享体验</div>
                        <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                    <?php } elseif($deal->end_date >= time()) { ?>
                        <form action="/deal/deal/check?sn=<?= $deal->sn ?>" method="post" id="order_form">
                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>"/>
                            <div class="dR-input">
                                <input type="text" class="dR-money" name="money" id="deal_money" placeholder="<?= StringUtils::amountFormat1('{amount}{unit}', $deal->start_money) ?>起借，<?= StringUtils::amountFormat1('{amount}{unit}', $deal->dizeng_money) ?>递增" autocomplete="off" value="<?= ($money > 0) ? $money : null ?>"/>
                                <!--输入款提示信息-->
                                <div class="tishi tishi-dev">
                                    <img class="jiao-left" src="/images/deal/jiao-right.png" alt="">
                                    <ul class="dR-tishi">
                                        <li><span>起借<?= StringUtils::amountFormat2($deal->start_money) ?>元</span></li>
                                        <li><span>递增<?= StringUtils::amountFormat2($deal->dizeng_money) ?>元</span></li>
                                    </ul>
                                </div>
                                <!--输入款错误提示信息-->
                                <div class="dR-tishi-error">
                                    <span  class="err_message" style="color: red;"></span>
                                </div>
                            </div>
                            <ul class="clearfix dR-inner dR-shouyi">
                                <li class="dR-inner-left">出借利息:</li>
                                <li class="dR-inner-right"><span><i id="expect_profit">0.00</i></span>元</li>
                            </ul>

                            <?php $couponCount = count($coupons); ?>
                            <?php if ($couponCount > 0 && $deal->allowUseCoupon) { ?>
                                <div id="coupons"></div>
                            <?php } ?>
                            <div>
                                <input type="submit" class="dR-btn" id="order_submit" value="立即出借"/>
                            </div>
                        </form>
                    <?php } else { ?>
                        <div class="dR-shouqing">标的已成立</div>
                        <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                    <?php }?>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_PRE) { ?>
                    <?php
                        $start = Yii::$app->functions->getDateDesc($deal['start_date']);
                        $deal->start_date = $start['desc'].date('H:i', $start['time']);
                    ?>
                    <div class="dR-shouqing"><?= $deal->start_date ?>起售</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_FOUND) { ?>
                    <div class="dR-shouqing">标的已成立</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_FULL) { ?>
                    <div class="dR-shouqing">标的已售罄</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_OVER) { ?>
                    <div class="dR-shouqing">标的已还清</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_HUAN) { ?>
                    <div class="dR-shouqing">标的募集完成，收益中...</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">查看其他标的</div>
                <?php } ?>

                <p style="padding-bottom: 0.5em; font-size: 12px; color: #aab2bd;">*市场有风险，出借需谨慎</p>
            </div>
        </div>
    </div>
</div>
<!--mask弹框-->
<div class="detail-mask"></div>
<!--确认弹框-->
<div class="confirmBox">
    <div class="confirmBox-title" style="height: auto">提示</div>
    <div class="confirmBox-top">
        <p></p>
    </div>
    <div class="confirmBox-bottom">
        <div class="confirmBox-left" style="width: 170px" onclick="subClose()">关闭</div>
        <div class="confirmBox-right" style="width: 170px" onclick="subConfirm()">确认</div>
    </div>
</div>

<script>
    $(function () {
        $('#tip').hover(function () {
            $('.dialog').stop(true, false).show();
        }, function () {
            $('.dialog').stop(true, false).hide();
        });

        //宽限期
        $('#kuanxian_tip').mouseover(function() {
            $('#kuanxian_message').fadeIn();
        }).mouseleave(function() {
            $('#kuanxian_message').fadeOut();
        });

        //获取出借记录
        getOrderList('/deal/deal/order-list?pid=<?= $deal->id ?>');
        $('#order_list').on('click', 'a', function (e) {
            e.preventDefault(); // 禁用a标签默认行为
            getOrderList($(this).attr('href'));
        });

        profit($('#deal_money'));

        //获取预期收益
        $('#deal_money').keyup(function () {
            profit($(this));

            var val = $(this).val();
            if (false == $.isNumeric(val) || ' ' == val.substring(val.length - 1, val.length)) {
                $(this).val(val.substring(0, val.length - 1));
            }
        });

        //提交表单
        $('#order_form').on('submit', function (e) {
            e.preventDefault();

            var money = $('#deal_money').val();
            var log = <?= Yii::$app->user->isGuest ? 0 : 1 ?>;

            if (log == 0) {
                login();

                return false;
            }

            if ('' === money) {
                $('.dR-tishi-error ').show();
                $('.dR-tishi-error .err_message').html('出借金额不能为空');

                return false;
            }

            order();
        });
    });

    function order() {
        var buy = $('#order_submit');
        var form = $('#order_form');
        var vals = form.serialize();

        buy.attr('disabled', true);

        var xhr = $.post(form.attr("action"), vals, function (data) {
            if (data.code == 0 && data.tourl) {
                location.href = data.tourl;
            } if (data.code == 5) {
                //location.href = data.tourl;
            } else {
                $('.dR-tishi-error ').show();
                //未免密不提示、不跳转，直接弹框
                if('/user/qpay/binding/umpmianmi' != data.tourl && '/user/identity' != data.tourl){
                    $('.dR-tishi-error .err_message').html(data.message);
                }
            }
            var currentUrl = encodeURIComponent(location.href);
            if ('/site/risk' == data.tourl) {
                //获取测评信息
                risk();
            } else if ('/site/login' == data.tourl) {
                //获取登录信息
                login();
            } else if ('/user/qpay/binding/umpmianmi' == data.tourl) {
                mianmi('/user/qpay/binding/umpmianmi?from=' + currentUrl);
            } else if ('/user/identity' == data.tourl) {
                window.location.href = '/user/userbank/identity?from=' + currentUrl;
            } else {
                if (data.tourl) {
                    location.href = data.tourl;
                }
            }

            if ('undefined' !== typeof data.confirm && 1 === data.confirm) {
                openPopup();
            }

            if (2 === data.code) {
                validForLoan();
            }
        });

        xhr.always(function () {
            buy.attr('disabled', false);
            buy.val("立即出借");
        });

        xhr.fail(function () {
            $('.dR-tishi-error ').show();
            $('.dR-tishi-error .err_message').html('系统繁忙，请稍后重试！');
        });
    }

    //处理ajax登录
    function login() {
        document.documentElement.style.overflow = 'hidden';   //禁用页面上下滚动效果

        //如果已经加载过登录页面，则直接显示
        if ($('.login-mark').length > 0) {
            $('.login-mark').fadeIn();
            $('.loginUp-box').fadeIn();
        } else {
            //加载登录页面
            getLoginHtml();
        }
    }

    //获取测评提示页
    function risk() {
        document.documentElement.style.overflow = 'hidden';   //禁用页面上下滚动效果
        $.ajax({
            beforeSend: function (req) {
                req.setRequestHeader("Accept", "text/html");
            },
            'url': '/site/risk',
            'type': 'get',
            'dataType': 'html',
            'success': function (html) {
                $('body').append(html);
                $('.login-mark').fadeIn();
                $('.loginUp-box').fadeIn();
            }
        });
        return '';
    }

    function chongzhi() {
        var guest = <?= intval(Yii::$app->user->isGuest) ?>;
        if (guest) {
            login();
        } else {
            location.href = '/user/recharge/init';
        }
    }

    //获取登录页面
    function getLoginHtml() {
        $.ajax({
            beforeSend: function (req) {
                req.setRequestHeader("Accept", "text/html");
            },
            'url': '/site/login-form',
            'type': 'get',
            'dataType': 'html',
            'success': function (html) {
                $('body').append(html);
                $('.login-mark').fadeIn();
                $('.loginUp-box').fadeIn();
            }
        });
        return '';
    }

    //获取投标记录
    function getOrderList(url) {
        $.ajax({
            beforeSend: function (req) {
                req.setRequestHeader("Accept", "text/html");
            },
            'url': url,
            'type': 'get',
            'dataType': 'html',
            'success': function (html) {
                $('#order_list').html(html);
            }
        });
    }

    //获取预期收益
    function profit($this) {
        var sn = "<?= $deal->sn ?>";
        var csrf = "<?= Yii::$app->request->csrfToken ?>";
        var money = $this.val();
        if(!!money){money = money.replace(/^[^0-9]+/, '');}
        if (!$.isNumeric(money)) {
            money = 0;
        }

        if (money <= 0) {
            $('#expect_profit').html('0.00');

            return;
        }

        $.post('/deal/deal/interest', {'sn': sn, '_csrf': csrf, 'amount': money}, function (data) {
            if (data && data.code === 0 && data.interest) {
                $('#expect_profit').html(WDJF.numberFormat(data.interest, false));
            } else {
                $('#expect_profit').html("0.00");
            }
        });
    }

    <?php if ($couponCount > 0 && $deal->allowUseCoupon) { ?>
        $(function () {
            validForLoan();

            //代金券选择
            $('#coupons').on('click', '#valid_coupon_list li', function (e) {
                e.preventDefault();

                var $this = $(this);
                var sn = '<?= $deal->sn ?>';
                var couponId = $this.attr('cid');
                var money = $('#deal_money').val();
                var opt = 'selected';

                if ($this.hasClass('picked-box')) {
                    opt = 'canceled';
                }

                request('/user/coupon/add-coupon', {sn: sn, couponId: couponId, money: money, opt: opt}, function (data) {
                    if (0 === data.code) {
                        $('.dR-tishi-error ').hide();
                        $('.dR-tishi-error .err_message').html('');

                        if ('selected' === opt) {
                            $this.addClass('picked-box');
                            $this.find('.quan-true').show();
                            $('#coupon_title').html('使用代金券'+data.data.money+'元（共'+data.data.total+'张）');
                        } else {
                            $this.removeClass('picked-box');
                            $this.find('.quan-true').hide();

                            if (data.data.money) {
                                $('#coupon_title').html('使用代金券'+data.data.money+'元（共'+data.data.total+'张）');
                            } else {
                                $('#coupon_title').html('<img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count"><?= $couponCount ?></i>');
                            }
                        }

                        $('#selectedCouponCount').val(data.data.total);
                        $('#selectedCouponAmount').val(data.data.money);
                    }
                }, function (data) {
                    if (data.code) {
                        $('.dR-tishi-error ').show();
                        $('.dR-tishi-error .err_message').html(data.message);

                        if (2 === data.code) {
                            validForLoan();
                        }
                    }
                });
            });

            //获取有效代金券
            $('#deal_money').on('keyup', $.throttle(500, function () {
                validForLoan();
            }));
        });

        function openPopup() {
            var validCouponCount = '<?= $couponCount ?>';
            var count = $('#selectedCouponCount').val();
            var amount = $('#selectedCouponAmount').val();
            var message = '';

            if (amount > 0) {
                message = '您有'+validCouponCount+'张代金券可用，目前已选择'+count+'张代金券可抵扣'+WDJF.numberFormat(amount, true)+'元';
            } else {
                message = '您有'+validCouponCount+'张代金券可用，本次未使用代金券抵扣，点击确定立即出借';
            }

            $('.confirmBox-top p').html(message);
            $('.mask').show();
            $('.confirmBox').show();
        }

        function subConfirm() {
            subClose();

            $('#couponConfirm').val('1');
            order();
            $('#couponConfirm').val('');
        }

        function subClose() {
            $('.mask').hide();
            $('.confirmBox').hide();
        }

        function validForLoan() {
            var sn = '<?= $deal->sn ?>';
            var money = $('#deal_money').val();

            if (money >= 0) {
                request('/user/coupon/valid', {sn: sn, money: money, _mark:Math.random()}, function (data) {
                    if (data) {
                        $('#coupons').html(data);
                    }
                });
            }
        }

        var allowClick = true;

        function request(url, params, success, fail) {
            if (!allowClick) {
                return;
            }

            allowClick = false;

            var xhr = $.get(url, params);

            xhr.done(function (data) {
                if ('function' === typeof success) {
                    success(data);
                }
            });

            xhr.fail(function(jqXHR) {
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);

                    if ('function' === typeof fail) {
                        fail(resp);
                    }
                } else {
                    alert('系统繁忙，请稍后重试！');
                }
            });

            xhr.always(function () {
                allowClick = true;
            });
        }
    <?php } ?>
</script>
