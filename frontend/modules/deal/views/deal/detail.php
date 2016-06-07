<?php
$this->title = '项目详情';
use common\view\LoanHelper;
use common\models\product\RateSteps;
use common\models\product\OnlineProduct;

$user = Yii::$app->user->identity;
$deal->money = rtrim(rtrim($deal->money, '0'), '.');
?>
<hr>
<p><?= $deal->title ?></p>
<ul class=" <?= $deal->isFlexRate ? 'rate-steps' : '' ?>">
    <li class="col-xs-6">
        <div>
            <?= LoanHelper::getDealRate($deal) ?>
            <span class="column-lu">%</span>
            <?php if (!empty($deal->jiaxi) && !$deal->isFlexRate) { ?>+<?= doubleval($deal->jiaxi) ?>%<?php } ?>
        </div>
        <div>
            年化收益率
        </div>
    </li>
    <li class="col-xs-6">
        <div>
            <?= $deal->expires ?>
            <?php if (1 === (int)$deal->refund_method) { ?>
                天
            <?php } else { ?>
                个月
            <?php } ?>
        </div>
        <div>
            项目期限
            <?php if (!empty($deal->kuanxianqi)) { ?>
                <i>(包含<?= $deal->kuanxianqi ?>天宽限期)</i> <img
                    src="<?= ASSETS_BASE_URI ?>images/dina.png" alt="">
            <?php } ?>
            <?php if (!empty($deal->kuanxianqi)) { ?>
                宽限期：应收账款的付款方因内部财务审核,结算流程或结算日遇银行非工作日等因素，账款的实际结算日可能有几天的延后
            <?php } ?>
        </div>
    </li>
</ul>
<div>
    <ul>
        <li>
            项目总额：<?= Yii::$app->functions->toFormatMoney($deal->money); ?>
        </li>
        <li>
            还款方式：<?= Yii::$app->params['refund_method'][$deal->refund_method] ?>
        </li>
        <li>
            项目起息：<?= $deal->jixi_time > 0 ? date('Y-m-d', $deal->jixi_time) : '项目成立日次日'; ?>
        </li>
        <li>
            <?php if (0 === (int)$deal->finish_date) { ?>
                项目期限：<?= $deal->expires ?>
                <?= (1 === (int)$deal->refund_method) ? '天' : '个月' ?>
            <?php } else { ?>
                项目结束：<?= date('Y-m-d', $deal->finish_date) ?>
            <?php } ?>
        </li>
        <li>
            募集进度：<?= number_format($deal->finish_rate * 100, 0) ?>%
        </li>
    </ul>
</div>
<?php if ($deal->isFlexRate) { ?>
    <div>
        收益说明
        <table>
            <tr>
                <th>认购金额(元)</th>
                <th>年化收益率(%)</th>
            </tr>
            <tr>
                <td>累计认购<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>起</td>
                <td><?= rtrim(rtrim(number_format($deal->yield_rate * 100, 2), '0'), '.') ?></td>
            </tr>
            <?php foreach (RateSteps::parse($deal->rateSteps) as $val) { ?>
                <tr>
                    <td>累计认购<?= rtrim(rtrim(number_format($val->min, 2), '0'), '.') ?>起</td>
                    <td><?= rtrim(rtrim(number_format($val->rate, 2), '0'), '.') ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php } ?>
<div>
    <p>项目详情</p>
    <div>
        <?= \yii\helpers\HtmlPurifier::process($deal->description) ?>
    </div>
</div>
<div>
    <p>投资记录</p>
    <div id="order_list"></div>
</div>
<div>
    <p>项目可投余额：<?= ($deal->status==1)?(Yii::$app->functions->toFormatMoney($deal->money)) : rtrim(rtrim(number_format($deal->getLoanBalance(), 2), '0'), '.').'元'?></p>
   <p> 我的可用余额：<?= (null === $user)?'查看余额请【<a href="/site/login">登录</a>】':($user->lendAccount?$user->lendAccount->available_balance.' 元':'0 元')?></p>
    <div>
        <?php if($deal->status == OnlineProduct::STATUS_NOW) {?>
            <form action="/order/order/doorder?sn=<?= $deal->sn ?>" method="post" id="order_form">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken?>"/>
                <input type="text"  name="money"  placeholder = "起投金额<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元，递增金额<?= rtrim(rtrim(number_format($deal->dizeng_money, 2), '0'), '.') ?>元" id="deal_money"/>
                <input type="submit" name="立即投资" id="order_submit"/>
                <p>
                    代金券 <span id="coupon_count">0</span>
                    <span id="coupon_title">无可用代金券</span>
                </p>
                <div id="valid_coupon_list">

                </div>
            </form>
        <?php } elseif($deal->status == OnlineProduct::STATUS_PRE){ ?>
            <?php
                $start = Yii::$app->functions->getDateDesc($deal['start_date']);
                $deal->start_date = $start['desc'] . date('H:i', $start['time']);
            ?>
           <?= $deal->start_date ?>起售
            <p>投资其他项目</p>
        <?php } else {?>
            项目募集完成，收益中...
            <p>投资其他项目</p>
        <?php }?>
    </div>
</div>

<hr>
<script>
    $(function () {
        //获取投资记录
        getOrderList('/deal/deal/order-list?pid=<?=$deal->id?>');
        //获取代金券
        $('#deal_money').keyup(function () {
            var money = $(this).val();
            $.ajax({
                beforeSend: function (req) {
                    req.setRequestHeader("Accept", "text/html");
                },
                'url': '/user/coupon/valid?sn=<?= $deal->sn?>&mmoney=' + money,
                'type': 'get',
                'dataType': 'html',
                'success': function (html) {
                    $('#coupon_count').html($(html).attr('data_count'));
                    $('#valid_coupon_list').html(html);
                    $('#valid_coupon_list .coupon_radio').bind('click', function () {
                        if ($(this).attr('checked')) {
                            $('#coupon_title').html($(this).parent().find('.coupon_name').text());
                        }
                    });
                }
            });
        });
        //提交表单
        var buy = $('#order_submit');
        var form = $('#order_form');
        form.on('submit', function (e) {
            e.preventDefault();

            if ($('#deal_money').val() == '') {
                toast('投资金额不能为空');
                return false;
            }

            buy.attr('disabled', true);
            buy.val("购买中……");
            var vals = form.serialize();
            var xhr = $.post(form.attr("action"), vals, function (data) {
                if (data.code == 0) {
                    //toast('投标成功');
                } else {
                    alert(data.message);
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

</script>

