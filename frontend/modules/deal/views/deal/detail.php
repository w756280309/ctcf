<?php

use common\view\LoanHelper;
use common\models\product\RateSteps;
use common\models\product\OnlineProduct;
use frontend\assets\FrontAsset;

$this->title = '项目详情';
$user = Yii::$app->user->identity;
$deal->money = rtrim(rtrim($deal->money, '0'), '.');

$this->registerJsFile('/js/lib.js');
$this->registerJsFile('/js/detail.js');
$this->registerCssFile('/css/deal/buy.css');
$this->registerCssFile('/css/deal/deallist.css');
$this->registerCssFile('/css/deal/detail.css');
$this->registerCssFile('/css/pagination.css');
FrontAsset::register($this);

?>
<div class="project-box clearfix">
    <div class="project-left clearfix">
        <!--pL-top-box-->
        <div class="pL-top-box">
            <div class="pl-top-title"><?= $deal->title ?></div>
            <div class="pl-middle">
                <ul class="clearfix">
                    <li>
                        <div class="clearfix">
                            <span class="pl-middle-inner">
                                <?= LoanHelper::getDealRate($deal) ?><i>%</i>
                                <em class="pl-m-add">
                                    <?php if (!empty($deal->jiaxi) && !$deal->isFlexRate) { ?>+<?= doubleval($deal->jiaxi) ?>%<?php } ?>
                                </em>
                            </span>
                            <p>年化收益率</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="pl-middle-inner">
                                 <?= $deal->expires ?>
                                <?php if (1 === (int)$deal->refund_method) { ?>
                                    <i>天</i>
                                <?php } else { ?>
                                    <i>个月</i>
                                <?php } ?>
                            </span>
                            <p>项目期限</p>

                            <?php if (!empty($deal->kuanxianqi)) { ?>
                                <p><i>(包含<?= $deal->kuanxianqi ?>天宽限期)</i> <img src="<?= ASSETS_BASE_URI ?>images/dina.png" alt=""></p>
                            <?php } ?>
                            <?php if (!empty($deal->kuanxianqi)) { ?>
                                <p>宽限期：应收账款的付款方因内部财务审核,结算流程或结算日遇银行非工作日等因素，账款的实际结算日可能有几天的延后</p>
                            <?php } ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="pl-middle-content"><?= Yii::$app->functions->toFormatMoney($deal->money) ?></span>
                            <p>项目总额</p>
                        </div>
                    </li>
                    <li>
                        <div class="pl-last-li">
                            <span class="pl-middle-last"><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="pl-bottom">
                <ul>
                    <li>
                        项目起息:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $deal->jixi_time > 0 ? date('Y-m-d', $deal->jixi_time) : '项目成立日次日'; ?></span>
                    </li>
                    <?php if (0 === (int)$deal->finish_date) { ?>
                        <li>
                            项目期限:&nbsp;&nbsp;&nbsp;&nbsp;
                            <span>
                                <?= $deal->expires ?> <?= (1 === (int)$deal->refund_method) ? '天' : '个月' ?>
                            </span>
                        </li>
                    <?php } else { ?>
                        <li>项目结束:&nbsp;&nbsp;&nbsp;&nbsp;<span><?= date('Y-m-d', $deal->finish_date) ?></span></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <?php if ($deal->isFlexRate) { ?>
            <!--pl-subscription-->
            <div class="pl-subscription">
                <ul class="clearfix">
                    <li class="pl-grayBg"><span>认购金额（元）</span></li>
                    <li class="pl-grayBg pl-center">年化收益率（%）</li>
                    <li>
                        <div><i>累计认购<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>起</i></div>
                    </li>
                    <li>
                        <div class="pl-rborder pl-center"><?= rtrim(rtrim(number_format($deal->yield_rate * 100, 2), '0'), '.') ?></div>
                    </li>
                    <?php foreach (RateSteps::parse($deal->rateSteps) as $val) { ?>
                        <li>
                            <div><i>累计认购<?= rtrim(rtrim(number_format($val['min'], 2), '0'), '.') ?>起</i></div>
                        </li>
                        <li>
                            <div class="pl-rborder pl-center"><?= rtrim(rtrim(number_format($val['rate'], 2), '0'), '.') ?></div>
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
                    <?= \yii\helpers\HtmlPurifier::process($deal->description) ?>
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
                        <span data-progress="<?= number_format($deal->finish_rate * 100, 0) ?>" style="width: <?= number_format($deal->finish_rate * 100, 0) ?>%;"></span>
                    </div>
                    <div class="dRP-data"><?= number_format($deal->finish_rate * 100, 0) ?>%</div>
                </div>
                <ul class="clearfix dR-inner">
                    <li class="dR-inner-left">项目可投余额：</li>
                    <li class="dR-inner-right">
                        <span><i><?= ($deal->status == 1) ? (Yii::$app->functions->toFormatMoney($deal->money)) : rtrim(rtrim(number_format($deal->getLoanBalance(), 2), '0'), '.') . '元' ?></i>元</span>
                    </li>
                    <li class="dR-inner-left">我的可用余额：</li>
                    <li class="dR-inner-right"><?= (null === $user) ? '查看余额请【<a href="/site/login">登录</a>】' : ($user->lendAccount ? number_format($user->lendAccount->available_balance, 2) . ' 元' : '0 元') ?></li>
                    <li class="dR-inner-left">投资金额(元)：</li>
                    <li class="dR-inner-right"><a href="/user/userbank/recharge">去充值</a></li>
                </ul>
                <!--已售罄-->
                <?php if ($deal->status == OnlineProduct::STATUS_NOW) { ?>
                    <form action="/order/order/doorder?sn=<?= $deal->sn ?>" method="post" id="order_form">
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>"/>
                        <div class="dR-input">
                            <input type="text" class="dR-money" name="money" id="deal_money"/>
                            <!--输入款提示信息-->
                            <div class="tishi">
                                <img class="jiao-left" src="/images/deal/jiao-right.png" alt="">
                                <ul class="dR-tishi">
                                    <li><span>起投金额<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元</span></li>
                                    <li><span>递增金额<?= rtrim(rtrim(number_format($deal->dizeng_money, 2), '0'), '.') ?>元</span></li>
                                </ul>
                            </div>
                            <!--输入款错误提示信息-->
                            <div class="dR-tishi-error">
                                <img class="jiao-left" src="/images/deal/jiao-right.png" alt="">
                                <ul class="dR-tishi">
                                    <li style="width: 83px;"> <span  class="err_message"></span> </li>
                                </ul>
                            </div>
                        </div>
                        <ul class="clearfix dR-inner dR-shouyi">
                            <li class="dR-inner-left">预计收益:</li>
                            <li class="dR-inner-right"><span><i id="expect_profit"></i></span>元</li>
                        </ul>

                        <!--待选代金券-->
                        <ul class="dR-down clearfix">
                            <li class="dR-down-left"  id="coupon_title"><img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count">0</i></li>
                            <li class="dR-down-right"><img src="/images/deal/down.png" alt=""></li>
                        </ul>
                        <!--代金券选择-->
                        <div class="dR-quan" id="valid_coupon_list">

                        </div>
                        <div>
                            <input type="submit" class="dR-btn" id="order_submit" value="立即投资"/>
                        </div>
                    </form>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_PRE) { ?>
                    <?php
                    $start = Yii::$app->functions->getDateDesc($deal['start_date']);
                    $deal->start_date = $start['desc'] . date('H:i', $start['time']);
                    ?>
                    <div class="dR-shouqing"><?= $deal->start_date ?>起售</div>
                    <div class="dR-btn" onclick="window.location = '/licai/index'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_FULL || $deal->status == OnlineProduct::STATUS_FOUND) { ?>
                    <div class="dR-shouqing">项目已售罄</div>
                    <div class="dR-btn" onclick="window.location = '/licai/index'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_OVER) { ?>
                    项目已还清
                    <div class="dR-btn" onclick="window.location = '/licai/index'">投资其他项目</div>
                <?php } elseif ($deal->status == OnlineProduct::STATUS_HUAN) { ?>
                    项目募集完成，收益中...
                    <div class="dR-btn" onclick="window.location = '/licai/index'">投资其他项目</div>
                <?php } else { ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        //获取投资记录
        getOrderList('/deal/deal/order-list?pid=<?=$deal->id?>');
        var money = $(this).val();
        //获取可用代金券
        <?php if(!Yii::$app->user->isGuest){ ?>
        $.ajax({
            beforeSend: function (req) {
                req.setRequestHeader("Accept", "text/html");
            },
            'url': '/user/coupon/valid?sn=<?= $deal->sn?>&mmoney=' + money,
            'type': 'get',
            'dataType': 'html',
            'success': function (html) {
                var count = $(html).attr('data_count');
                $('#coupon_count').html($(html).attr('data_count'));
                $('#valid_coupon_list').html(html);
                $('.dR-quan li').on('click', function () {
                    var index = $('.dR-quan li').index(this);
                    if('none' ==  $('.quan-true').eq(index).css('display')){
                        $('.quan-true').hide();
                        $('.quan-true').eq(index).show();
                        $(this).find('.coupon_radio').attr('checked', true);
                        $('#coupon_title').html($(this).find('.coupon_name').text());
                    }else{
                        $('.quan-true').hide();
                        $(this).find('.coupon_radio').removeAttr('checked');
                        $('#coupon_title').html('<img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count">'+count+'</i>');
                    }

                });
            }
        });
        <?php }?>
        //获取预期收益
        $('#deal_money').keyup(function () {
            profit($(this));
        });
        //提交表单
        var buy = $('#order_submit');
        var form = $('#order_form');
        form.on('submit', function (e) {
            e.preventDefault();
            if ($('#deal_money').val() == '') {
                $('.dR-tishi-error ').show();
                $('.dR-tishi-error .err_message').html('投资金额不能为空');
                return false;
            }
            buy.attr('disabled', true);
            buy.val("购买中……");
            var vals = form.serialize();
            var xhr = $.post(form.attr("action"), vals, function (data) {
                if (data.code == 0) {
                    //toast('投标成功');
                } else {
                    $('.dR-tishi-error ').show();
                    $('.dR-tishi-error .err_message').html(data.message);
                }
                if (data.tourl != undefined) {
                    setTimeout(function () {
                        location.replace(data.tourl);
                    }, 1000);
                }
            });
            xhr.always(function () {
                buy.attr('disabled', false);
                buy.val("购买");
            })
        });

    });
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
                $('#order_list .pagination li a').on('click', function (e) {
                    e.preventDefault(); // 禁用a标签默认行为
                    getOrderList($(this).attr('href'));
                })
            }
        });
    }

    //获取预期收益
    function profit($this) {
        var yr = "<?= $deal->yield_rate ?>";
        var qixian = "<?= $deal->expires ?>";
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
                    $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 365), false) + "元");
                } else {
                    $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, rate), qixian), 12), false) + "元");
                }
            });
        } else {
            if (1 == parseInt(retmet)) {
                $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 365), false) + "元");
            } else {
                $('#expect_profit').html(WDJF.numberFormat(accDiv(accMul(accMul(money, yr), qixian), 12), false) + "元");
            }
        }
    }
</script>
