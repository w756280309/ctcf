<?php

use common\models\product\RateSteps;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\view\LoanHelper;
use yii\helpers\HtmlPurifier;

$this->title = '项目详情';
$user = Yii::$app->user->identity;

$this->registerJsFile(ASSETS_BASE_URI . 'js/detail.js?v=161227');
$this->registerCssFile(ASSETS_BASE_URI . 'css/deal/buy.css');
$this->registerCssFile(ASSETS_BASE_URI . 'css/deal/deallist.css?v=161124');
$this->registerCssFile(ASSETS_BASE_URI . 'css/deal/detail.css?v=161218');
$this->registerCssFile(ASSETS_BASE_URI . 'css/pagination.css');
$this->registerCssFile(ASSETS_BASE_URI . 'css/useraccount/chargedeposit.css');

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
                                    <?php if (!empty($deal->jiaxi)) { ?>+<?= doubleval($deal->jiaxi) ?>%<?php } ?>
                                </em>
                            </span>
                            <p>预期年化收益率</p>
                        </div>
                    </li>
                    <li>
                        <div class="expires">
                            <span class="pl-middle-inner">
                                <?php $ex = $deal->getDuration(); ?><?= $ex['value']?><i><?= $ex['unit'] ?></i>
                            </span>
                            <p>项目期限</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="pl-bottom">
                <ul>
                    <li class="amount">
                        项目总额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat1('{amount}{unit}', $deal->money) ?></span>
                    </li>
                    <li>
                        还款方式:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></span>
                        <?php if ($deal->isNatureRefundMethod()) { ?>
                            <img id="tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                            <div class="dialog">付息时间固定日期，按自然月在每个月还款，按自然季度是3、6、9、12月还款，按自然半年是6、12月还款，按自然年是12月还款</div>
                        <?php } ?>
                    </li>
                    <li class="amount">
                        产品起息日:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $deal->jixi_time > 0 ? date('Y-m-d', $deal->jixi_time) : '项目成立日次日'; ?></span>
                    </li>
                    <?php if (0 === (int) $deal->finish_date) { ?>
                        <li>
                            项目期限:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span>
                                <?php $ex = $deal->getDuration(); ?><?= $ex['value']?><?= $ex['unit'] ?>
                            </span>
                        </li>
                    <?php } else { ?>
                        <li>产品到期日:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= date('Y-m-d', $deal->finish_date) ?></span></li>
                    <?php } ?>
                    <li class="amount">
                        起投金额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat2($deal->start_money) ?>元</span>
                    </li>
                    <li>
                        递增金额:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= StringUtils::amountFormat2($deal->dizeng_money) ?>元</span>
                    </li>
                </ul>
            </div>

            <?php if (!empty($deal->kuanxianqi)) { ?>
                <p class="grace-period">融资方可提前<?= $deal->kuanxianqi ?>天内任一天还款，客户收益按实际天数计息。</p>
            <?php } ?>

            <?php if ($deal->is_xs) { ?>
                <p class="grace-period notice-coupon">新手专享标每账户限投资一次，限购一万元。</p>
            <?php } ?>

            <?php if (!$deal->allowUseCoupon) { ?>
                <p class="grace-period notice-coupon">此项目不参与活动，不可使用代金券。</p>
            <?php } ?>
        </div>
        <?php if ($deal->isFlexRate) { ?>
            <!--pl-subscription-->
            <div class="pl-subscription">
                <ul class="clearfix">
                    <li class="pl-grayBg"><span>认购金额（元）</span></li>
                    <li class="pl-grayBg pl-center">预期年化收益率（%）</li>
                    <li>
                        <div><i>累计认购<?= StringUtils::amountFormat2($deal->start_money) ?>起</i></div>
                    </li>
                    <li>
                        <div class="pl-rborder pl-center"><?= StringUtils::amountFormat2($deal->yield_rate * 100) ?></div>
                    </li>
                    <?php foreach (RateSteps::parse($deal->rateSteps) as $val) { ?>
                        <li>
                            <div><i>累计认购<?= StringUtils::amountFormat2($val['min']) ?>起</i></div>
                        </li>
                        <li>
                            <div class="pl-rborder pl-center"><?= StringUtils::amountFormat2($val['rate']) ?></div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <!--pl-detail-->
        <div class="pl-detail">
            <div class="plD-title">
                <div class="plD-btn plD-redBorder">
                    <div class="plD-redBorder">项目详情</div>
                </div>
                <div class="plD-btn">
                    <div>投资记录</div>
                </div>
            </div>
            <div class="plD-content show">
                <div class="plD-inner-box">
                    <?= HtmlPurifier::process($deal->description) ?>
                </div>
            </div>
            <div class="plD-content hide" id="order_list"></div>
        </div>
    </div>
    <div class="project-right">
        <div class="pR-box clearfix">
            <div class="pR-box-inner">
                <div class="pR-title">募集进度：</div>
                <div class="dR-progress-box">
                    <div class="dR-progress">
                        <span data-progress="<?= $deal->getProgressForDisplay()?>" style="width: <?= $deal->getProgressForDisplay()?>%;"></span>
                    </div>
                    <div class="dRP-data"><?= $deal->getProgressForDisplay()?>%</div>
                </div>
                <ul class="clearfix dR-inner">
                    <li class="dR-inner-left">项目可投余额：</li>
                    <li class="dR-inner-right">
                        <span><i>
                                <?= StringUtils::amountFormat2($deal->getLoanBalance())?>元
                            </i></span>
                    </li>
                    <li class="dR-inner-left">我的可用余额：</li>
                    <li class="dR-inner-right"><?= (null === $user) ? '查看余额请【<a onclick="login()" style="cursor: pointer">登录</a>】' : ($user->lendAccount ? StringUtils::amountFormat3($user->lendAccount->available_balance).' 元' : '0 元') ?></li>
                    <?php if ($deal->status == OnlineProduct::STATUS_NOW && $deal->end_date >= time()) { ?>
                        <li class="dR-inner-left">投资金额(元)：</li>
                        <li class="dR-inner-right"><a style="cursor: pointer" onclick="chongzhi()">去充值</a></li>
                    <?php } ?>
                </ul>
                <!--已售罄-->
                <?php if ($deal->status == OnlineProduct::STATUS_NOW) { ?>
                    <?php if (null !== $user && $deal->is_xs && $user->xsCount() >= Yii::$app->params['xs_trade_limit']) { ?>
                        <div class="dR-shouqing">您已经参与过新手专享体验</div>
                        <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                    <?php } elseif($deal->end_date >= time()) { ?>
                        <form action="/deal/deal/check?sn=<?= $deal->sn ?>" method="post" id="order_form">
                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>"/>
                            <div class="dR-input">
                                <input type="text" class="dR-money" name="money" id="deal_money" placeholder="<?= StringUtils::amountFormat1('{amount}{unit}', $deal->start_money) ?>起投，<?= StringUtils::amountFormat1('{amount}{unit}', $deal->dizeng_money) ?>递增" autocomplete="off" value="<?= ($money > 0) ? $money : null?>"/>
                                <!--输入款提示信息-->
                                <div class="tishi tishi-dev">
                                    <img class="jiao-left" src="/images/deal/jiao-right.png" alt="">
                                    <ul class="dR-tishi">
                                        <li><span>起投<?= StringUtils::amountFormat2($deal->start_money) ?>元</span></li>
                                        <li><span>递增<?= StringUtils::amountFormat2($deal->dizeng_money) ?>元</span></li>
                                    </ul>
                                </div>
                                <!--输入款错误提示信息-->
                                <div class="dR-tishi-error">
                                    <span  class="err_message" style="color: red;"></span>
                                </div>
                            </div>
                            <ul class="clearfix dR-inner dR-shouyi">
                                <li class="dR-inner-left">预计收益:</li>
                                <li class="dR-inner-right"><span><i id="expect_profit">0.00</i></span>元</li>
                            </ul>

                            <?php if (count($data) > 0 && $deal->allowUseCoupon) { ?>
                                <!--待选代金券-->
                                <ul class="dR-down clearfix">
                                    <li class="dR-down-left"  id="coupon_title"><img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count"><?= count($data) ?></i></li>
                                    <li class="dR-down-right"><img src="/images/deal/down.png" alt=""></li>
                                </ul>
                                <!--代金券选择-->
                                <div class="dR-quan" id="valid_coupon_list">
                                    <input type="hidden" name="couponId" class="hide_coupon" value="<?= $coupon_id ?>">
                                    <ul>
                                        <?php foreach ($data as $v) { ?>
                                            <li class="quan-false<?php if ($v->id === $coupon_id) { ?> picked-box<?php } ?>" cid="<?= $v->id ?>">
                                                <div class="quan-left">
                                                    <span>￥</span><?= number_format($v->couponType->amount) ?>
                                                </div>
                                                <div class="quan-right">
                                                    <div class="quan-right-content">
                                                        <div><?= $v->couponType->name ?></div>
                                                        <p>
                                                            单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $v->couponType->minInvest) ?>可用</p>
                                                        <p  class="coupon_name" style="display: none"> 单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $v->couponType->minInvest) ?>可抵扣<?= $v->couponType->amount ?>元</p>
                                                        <p>新手标、转让不可用</p>
                                                        <p>有效期至<?= $v->expiryDate ?></p>
                                                    </div>
                                                </div>
                                                <img class="quan-true <?php if ($v->id === $coupon_id) { ?>show<?php } ?>" src="/images/deal/quan-true.png" alt="">
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>
                            <div>
                                <input type="submit" class="dR-btn" id="order_submit" value="立即投资"/>
                            </div>
                        </form>
                    <?php } else { ?>
                        <div class="dR-shouqing">项目已成立</div>
                        <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                    <?php }?>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_PRE) { ?>
                    <?php
                        $start = Yii::$app->functions->getDateDesc($deal['start_date']);
                        $deal->start_date = $start['desc'].date('H:i', $start['time']);
                    ?>
                    <div class="dR-shouqing"><?= $deal->start_date ?>起售</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_FOUND) { ?>
                    <div class="dR-shouqing">项目已成立</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_FULL) { ?>
                    <div class="dR-shouqing">项目已售罄</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_OVER) { ?>
                    <div class="dR-shouqing">项目已还清</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_HUAN) { ?>
                    <div class="dR-shouqing">项目募集完成，收益中...</div>
                    <div class="dR-btn" onclick="window.location = '/licai'">投资其他项目</div>
                <?php } ?>

                <p style="padding-bottom: 0.5em; font-size: 12px; color: #bababf;">*理财非存款，产品有风险，投资须谨慎</p>
            </div>
        </div>
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
        $('#kuanxian_tip').mouseover(function(){
            $('#kuanxian_message').fadeIn();
        }).mouseleave(function(){
            $('#kuanxian_message').fadeOut();
        });

        //回退时保持选中状态，并保持代金券单笔投资限额提示信息
        var coupon_id = <?= $coupon_id ?>;
        if (coupon_id > 0) {
            if ($('.picked-box') && $('.picked-box').length > 0) {
                $('#coupon_title').html($('.picked-box').find('.coupon_name').text());
                $("#valid_coupon_list").show();
            }
        }

        //获取投资记录
        getOrderList('/deal/deal/order-list?pid=<?=$deal->id?>');
        $('#order_list').on('click', 'a', function (e) {
            e.preventDefault(); // 禁用a标签默认行为
            getOrderList($(this).attr('href'));
        });

        var money = $(this).val();
        //代金券选择
        $('#valid_coupon_list li').bind('click', function () {
            if (!$(this).hasClass('picked-box')) {
                $('.hide_coupon').val($(this).attr('cid'));
                $(this).addClass('picked-box').siblings().removeClass('picked-box');
                $(this).find('.quan-true').show().end().siblings().find('.quan-true').hide();
                $('#coupon_title').html($(this).find('.coupon_name').text());
            } else {
                $('.hide_coupon').val('');
                $(this).removeClass('picked-box').find('.quan-true').hide();
                $('#coupon_title').html('<img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count"><?= count($data)?></i>');
            }

        });
        //获取预期收益
        $('#deal_money').keyup(function () {
            profit($(this));
            var val = $(this).val();
            if (false == $.isNumeric(val) || ' ' == val.substring(val.length - 1, val.length)) {
                $(this).val(val.substring(0, val.length - 1));
            }
        });

        var guest = <?= intval(Yii::$app->user->isGuest)?>;//登录状态
        var rest = <?= ($deal->status == 1) ? 0 : floatval($deal->getLoanBalance())?>;
        var start = <?= $deal->start_money ?>;
        $('#deal_money').blur(function () {
            if (guest == 1){
                return false;
            }
            //判断起投金额
            var money = $(this).val();
            if (money) {
                if (rest >= start) {
                    if (money < start) {
                        $('.dR-tishi-error ').show();
                        $('.dR-tishi-error .err_message').html('投资金额小于起投金额（<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元）');
                        return false;
                    }
                }
            }
        });
        //提交表单
        var buy = $('#order_submit');
        var form = $('#order_form');
        form.on('submit', function (e) {
            e.preventDefault();
            var money = $('#deal_money').val();
            var log = <?= Yii::$app->user->isGuest ? 0 : 1 ?>;

            if (log == 0) {
                login();
                return false;
            }
            if (money > 0) {
                if (rest >= start) {
                    if (money < start) {
                        $('.dR-tishi-error ').show();
                        $('.dR-tishi-error .err_message').html('投资金额小于起投金额（<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元）');
                        return false;
                    }
                }
            } else {
                $('.dR-tishi-error ').show();
                $('.dR-tishi-error .err_message').html('投资金额不能为空');
                return false;
            }
            buy.attr('disabled', true);
            var vals = form.serialize();
            var xhr = $.post(form.attr("action"), vals, function (data) {
                if (data.code == 0 && data.tourl) {
                    location.href = data.tourl;
                } else {
                    $('.dR-tishi-error ').show();
                    //未免密不提示、不跳转，直接弹框
                    if('/user/qpay/binding/umpmianmi' != data.tourl && '/user/identity' != data.tourl){
                        $('.dR-tishi-error .err_message').html(data.message);
                    }
                }
                var currentUrl = encodeURIComponent(location.href);
                if ('/site/login' == data.tourl) {
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
            });
            xhr.always(function () {
                buy.attr('disabled', false);
                buy.val("立即投资");
            });

            xhr.fail(function () {
                $('.dR-tishi-error ').show();
                $('.dR-tishi-error .err_message').html('系统繁忙，请稍后重试！');
            });
        });

    });
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

    function chongzhi(){
        var guest = <?= intval(Yii::$app->user->isGuest)?>;
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
    function getOrderList(url)
    {
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
        var yr = "<?= $deal->yield_rate ?>";
        var qixian = "<?= $deal->getDuration()['value'] ?>";
        var retmet = "<?= $deal->refund_method ?>";
        var sn = "<?= $deal->sn ?>";
        var isFlexRate = <?= $deal->isFlexRate ?>;
        var csrf = "<?= Yii::$app->request->csrfToken ?>";
        var money = $this.val();
        money = money.replace(/^[^0-9]+/, '');
        if (!$.isNumeric(money)) {
            money = 0;
        }
        if (isFlexRate) {
            $.post('/deal/deal/rate', {'sn': sn, '_csrf': csrf, 'amount': money}, function (data) {
                if (true === data.res) {
                    rate = data.rate;
                } else {
                    rate = yr;
                }
                if (1 == parseInt(retmet)) {
                    $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 365), false));
                } else {
                    $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 12), false));
                }
            });
        } else {
            if (1 == parseInt(retmet)) {
                $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 365), false));
            } else {
                $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 12), false));
            }
        }
    }
</script>
