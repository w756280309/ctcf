<?php
$this->title = '转让详情';

use frontend\assets\FrontAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/credit.css?v=161025', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/credit/detail.js?v=160922', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.ba-throttle-debounce.min.js?v=161008', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css');
$this->registerCssFile(ASSETS_BASE_URI . 'css/useraccount/chargedeposit.css');

use common\utils\StringUtils;

$note_config = json_decode($respData['config'], true);

$nowTime = new \DateTime();
$endTime = new \DateTime($respData['endTime']);
$isClosed = $respData['isClosed'] || $nowTime >= $endTime;
?>

<div class="project-box clearfix">
    <div class="project-left clearfix">
        <!--pL-top-box-->
        <div class="pL-top-box">
            <div class="pl-top-title"><span>【转让】</span><div class="a-title"><?= $loan->title ?></div>
                <a class="a-project" href="/deal/deal/detail?sn=<?= $loan->sn ?>" target="_blank">查看原始项目</a>
                <div class="clear"></div>
            </div>
            <div class="pl-middle">
                <ul class="clearfix">
                    <li>
                        <div class="clearfix pl-middle-fl">
                            <span class="pl-middle-inner"><?= StringUtils::amountFormat2($order->yield_rate * 100) ?><i>%</i></span>
                            <p>预期年化收益率</p>
                        </div>
                    </li>
                    <li>
                        <div class="clearfix pl-middle-rg">
                            <span class="pl-middle-inner">
                                <?php
                                    $remainingDuration = $loan->getRemainingDuration();
                                    if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                                        echo $remainingDuration['months'] . '<i>个月</i>';
                                    }
                                    if (isset($remainingDuration['days'])) {
                                        if (!isset($remainingDuration['months']) || $remainingDuration['days'] >0) {
                                            echo $remainingDuration['days'] . '<i>天</i>';
                                        }
                                    }
                                ?>
                            </span>
                            <p>剩余期限</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="pl-bottom">
                <ul>
                    <li><span>转让金额:</span><?= StringUtils::amountFormat1('{amount}{unit}', bcdiv($respData['amount'], 100, 2)) ?></li>
                    <li><span>折让率:</span><?= StringUtils::amountFormat3($respData['discountRate']) ?>%<img class="credi-tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                        <div class="dialog tips">产品在转让时折让的比率，如折让率1%，则按照转让价值的99%来出售。<img class="tra-tip" src="<?= ASSETS_BASE_URI ?>images/tra-tip.png" alt=""> </div>
                    </li>
                    <li><span>转让起息日:</span>购买日次日</li>
                    <li><span>产品到期日:</span><?= date('Y-m-d', $loan->finish_date) ?></li>
                    <li><span>预期剩余收益:</span><?= StringUtils::amountFormat3(bcdiv($respData['remainingInterest'], 100, 2)) ?>元</li>

                    <!-- 按自然半年付息时  -->
                    <li class="last-li"><span>还款方式:</span><?= Yii::$app->params['refund_method'][$loan->refund_method] ?>
                        <?php if ($loan->isNatureRefundMethod()) { ?>
                            <img class="credi-tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                            <div class="halfyear-dialog tips">付息时间固定日期，按自然月在每个月还款，按自然季度是3、6、9、12月还款，按自然半年是6、12月还款，按自然年是12月还款。<img class="tra-tip" src="<?= ASSETS_BASE_URI ?>images/tra-tip.png" alt=""> </div>
                        <?php } ?>
                    </li>
                </ul>
            </div>
            <!--  下面这句 只在特定情况下,显示-->
            <?php if (!empty($loan->kuanxianqi)) { ?>
                <div class="credit-tips clear">融资方可提前<?= $loan->kuanxianqi ?>天内任一天还款，客户收益按实际天数计息。</div>
            <?php } ?>
        </div>

        <!--pl-detail-->
        <div class="pl-detail">
            <div class="plD-title">
                <div class="plD-btn"><div>转让记录</div></div>
            </div>
            <div class="plD-content" id="order_list"></div>
        </div>
    </div>
    <div class="project-right">
        <div class="pR-box clearfix">
            <div class="pR-box-inner">
                <div class="pR-title">转让进度：</div>
                <div class="dR-progress-box">
                    <div class="dR-progress">
                        <?php $progress = $isClosed ? 100 : bcdiv(bcmul($respData['tradedAmount'], 100), $respData['amount'], 0); ?>
                        <span data-progress="<?= $progress ?>"></span>
                    </div>
                    <div class="dRP-data"><?= $progress ?>%</div>
                </div>
                <ul class="clearfix dR-inner">
                    <li class="dR-inner-left">可交易金额：</li>
                    <li class="dR-inner-right"><span><i><?= $isClosed ? '0.00' : StringUtils::amountFormat2(bcdiv(bcsub($respData['amount'], $respData['tradedAmount']), 100, 2)) ?></i>元</span></li>
                    <?php if (empty($user)) { ?>
                        <li class="dR-inner-left">我的可用余额：</li>
                        <li class="dR-inner-right">查看余额请 <a href="javascript:login()">[登录]</a></li>
                    <?php } else { ?>
                        <li class="dR-inner-left">我的可用余额：</li>
                        <li class="dR-inner-right"><?= StringUtils::amountFormat3($user->lendAccount->available_balance) ?>元</li>
                    <?php } ?>

                    <?php if (!$isClosed) { ?>
                        <li class="dR-inner-left">认购金额：</li>
                        <li class="dR-inner-right"><a href="javascript:recharge()">去充值</a></li>
                    <?php } ?>
                </ul>
                <?php if (!$isClosed) { ?>
                <form action="/credit/note/check?id=<?= $respData['id'] ?>" method="post" id="note_order">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>" />
                    <div class="dR-input">
                        <input type="text" autocomplete="off" name="amount" class="dR-money">
                        <!--输入款提示信息-->
                        <div class="tishi tishi-dev">
                            <img class="jiao-left" src="/images/deal/jiao-right.png" alt="">
                            <ul class="dR-tishi">
                                <li><span>起投<?= StringUtils::amountFormat2(bcdiv($note_config['min_order_amount'], 100, 2)) ?>元</span></li>
                                <li><span>递增<?= StringUtils::amountFormat2(bcdiv($note_config['incr_order_amount'], 100, 2)) ?>元</span></li>
                            </ul>
                        </div>
                        <!--输入款错误提示信息-->
                        <div class="dR-tishi-error">
                            <span></span>
                        </div>
                    </div>
                    <ul class="clearfix dR-inner dR-shouyi">
                        <li class="dR-inner-left dR-lixi">应付利息: </li>
                        <li class="dR-inner-right yingfu"><span><i>0.00</i></span>元</li>
                    </ul>
                    <ul class="clearfix dR-inner dR-shouyi">
                        <li class="dR-inner-left">预计收益: </li>
                        <li class="dR-inner-right yuqi"><span><i>0.00</i></span>元</li>
                    </ul>

                    <input type="submit" class="dR-btn" id="order_submit" value="立即投资" />
                </form>
                <?php } else { ?>
                    <div class="over-credit">转让完成...</div>
                    <a href="/licai/" class="dR-btn">查看其他项目</a>
                <?php } ?>

                <p class="fengxian-tip">*理财非存款，产品有风险，投资须谨慎</p>
                <p class="risk-note"><a href="/credit/note/risk-note?type=2&loanId=<?= $respData['loan_id'] ?>" target="_blank">查看并确认《风险提示》</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    function callback(data)
    {
        if (data.interest) {
            $('.yingfu i').html(data.interest);
        }
        if (data.profit) {
            $('.yuqi i').html(data.profit);
        }
    }

    $(function() {
        $('.credi-tip').hover(function () {
            $(this).parent().find(".tips").stop(true, false).show();
        }, function () {
            $(this).parent().find(".tips").stop(true, false).hide();
        });

        //获取投资记录
        getOrderList('/credit/note/orders?id=<?= $respData['id'] ?>');
        $('#order_list').on('click', 'a', function (e) {
            e.preventDefault(); // 禁用a标签默认行为
            getOrderList($(this).attr('href'));
        });

        $('.dR-money').keyup($.throttle(100, function () {
            var amount = $(this).val();
            if (!$.isNumeric(amount) || ' ' === amount.substring(amount.length - 1, amount.length)) {
                $(this).val(amount.substring(0, amount.length - 1));
            }

            if ('0' === amount || '' === amount) {
                $('.yingfu i').html('0.00');
                $('.yuqi i').html('0.00');

                return;
            }

            //请求交易系统计算进行计算
            $.ajax({
                type: "get",
                url: "<?= rtrim(\Yii::$app->params['clientOption']['host']['tx_www'], '/')?>/credit-note/calc",
                data: {note_id:<?= $respData['id']?>, amount: amount},
                dataType: "jsonp"
            });
            //计算预期收益,再次调用计算应付利息与预计收益的函数

        }));

        $('.dR-money').blur(function () {
            $.post(note_form.attr('action'), note_form.serialize(), function (data) {
                if (1 === data.code) {
                    if (typeof data.tourl === 'undefined' || '' === data.tourl) {
                        $('.dR-tishi-error').show();
                        $('.dR-tishi-error').html(data.message);
                    }
                }
            });
        })

        var note_form = $('#note_order');
        var note_button = $('#order_submit');
        note_form.on('submit', function (e) {
            e.preventDefault();
            note_button.attr('disabled', 'disabled');

            var xhr = $.post(note_form.attr('action'), note_form.serialize(), function (data) {
                if (data.code == 0 && data.tourl) {
                    location.href = data.tourl;
                } else {
                    if ('/site/login' == data.tourl) {
                        //获取登录信息
                        login();
                    } else if('/user/qpay/binding/umpmianmi' == data.tourl){
                        mianmi();
                    } else if('/user/userbank/idcardrz' == data.tourl){
                        location.href = '/user/userbank/identity';
                    } else {
                        if (data.tourl) {
                            location.href = data.tourl;
                        }
                        $('.dR-tishi-error').show();
                        $('.dR-tishi-error').html(data.message);
                    }
                }
            });

            xhr.always(function () {
                note_button.attr('disabled', false);
                note_button.val("立即投资");
            });

            xhr.fail(function () {
                $('.dR-tishi-error').show();
                $('.dR-tishi-error').html('系统繁忙，请稍后重试！');
            });

        });
    });

    //处理ajax登录
    function login()
    {
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

    function recharge()
    {
        var isGuest = <?= Yii::$app->user->isGuest ? 'true' : 'false' ?>;
        if (isGuest) {
            login();
        } else {
            location.href = '/user/recharge/init';
        }
    }

    //获取登录页面
    function getLoginHtml()
    {
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
</script>
