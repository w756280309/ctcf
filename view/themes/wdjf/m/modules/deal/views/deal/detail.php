<?php

use common\models\product\RateSteps;
use common\models\product\RedeemHelper;
use common\utils\StringUtils;
use common\view\LoanHelper;
use wap\assets\WapAsset;
use yii\helpers\HtmlPurifier;

$this->title = '项目详情';

$this->registerCssFile(FE_BASE_URI.'libs/videojs/video-js.min.css', ['position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI.'css/xiangqing.css?v=20171218', ['depends' => WapAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI.'libs/videojs/video.min.js', ['position' => 1]);

?>
<style>
	body {
		-webkit-text-size-adjust: 100% !important;
	}
    .earn-ransom ol li {
        list-style: disc;
    }
</style>
<div class="row column">
    <div class="hidden-xs col-sm-1"></div>
    <div class="col-xs-12 col-sm-10 column-title">
        <span><?= $deal->title ?></span>
        <div class="details-tag">
            <?php if ($allowTransfer) { ?>
                <i>可转让</i>
            <?php } ?>
            <?php if ($deal->allowUseCoupon) { ?>
                <i>可用券</i>
            <?php } ?>
            <?php if ($pointsMultiple > 1) { ?>
                <i>积分<?= $pointsMultiple ?>倍</i>
            <?php }?>
            <?php if ($deal->tags && false !== array_search('慈善专属', explode('，', $deal->tags))) { ?>
                <i>慈善专属</i>
            <?php } ?>
        </div>
    </div>
    <div class="container">
        <ul class="row column-content <?= $deal->isFlexRate ? 'rate-steps' : '' ?>">
            <li class="col-xs-6">
                <div class="xian">
                    <?= LoanHelper::getDealRate($deal) ?><span class="column-lu">%</span>
                    <?php if (!empty($deal->jiaxi)) { ?><span class="bonus-badge">+<?=  doubleval($deal->jiaxi) ?>%</span><?php } ?>
                </div>
                <span class="qing">预期年化收益率</span>
            </li>
            <li class="col-xs-6">
                <div>
                    <?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><span class="column-lu"><?= $ex['unit']?></span>
                </div>
                <span class="qing">期限</span>
            </li>
        </ul>
    </div>
    <div class="hidden-xs col-sm-1"></div>
</div>

<div class="row bili">
    <div class="col-xs-12">
        <div class="per">
            <div class="progress-bar progress-bar-red" style="width:<?= $deal->getProgressForDisplay() ?>%"></div>
        </div>
    </div>
</div>

<div class="row shuju">
    <div class="col-xs-9 amt">
        <span><?= $deal->status == 1 ? StringUtils::amountFormat1('{amount}{unit}', $deal->money) : StringUtils::amountFormat2($deal->getLoanBalance()).'元' ?></span><i>/<?= StringUtils::amountFormat1('{amount}{unit}', $deal->money) ?></i>
        <div>可投余额/项目总额</div>
    </div>
    <div class="col-xs-2 progress-detail">
        <div class="shuju-bili"><?= $deal->getProgressForDisplay() ?><em>%</em></div>
    </div>
    <div class="col-xs-1"></div>
</div>

<div class="row message">
    <div class="col-xs-12 xian2">
        <?php if ($deal->is_xs) { ?>
            <p class="notice-coupon">新手专享标每账户限投资一次，限购一万元。</p>
        <?php } ?>
        <?php if (!$deal->allowUseCoupon) { ?>
            <p class="notice-coupon">此项目不参与活动，不可使用代金券。</p>
        <?php } ?>
        <div class="m1">起投金额：<span><?= StringUtils::amountFormat2($deal->start_money) ?>元</span></div>
        <div class="m5">递增金额：<span><?= StringUtils::amountFormat2($deal->dizeng_money) ?>元</span></div>   <!-- 增加递增金额字段 -->
        <div class="m2">产品起息日：<span><?= $deal->jixi_time > 0 ? date('Y-m-d',$deal->jixi_time) : '项目成立日次日';?></span></div>
        <?php if (0 === (int)$deal->finish_date) { ?>
            <div class="m3">项目期限：<span><?php $ex = $deal->getDuration() ?><?= $ex['value'] ?></span><?= $ex['unit']?></div>
        <?php } else { ?>
            <div class="m3">产品到期日：<span><?= date('Y-m-d',$deal->finish_date) ?></span></div>
        <?php } ?>

        <?php
            $refundMethodDescription = '';
            $refundMethodTitle = Yii::$app->params['refund_method'][$deal->refund_method];
            if ($deal->isNatureRefundMethod()) {
                $refundMethodDescription = '付息时间固定日期，按自然月在每个月还款，按自然季度是3、6、9、12月还款，按自然半年是6、12月还款，按自然年是12月还款';
            }
            if (\common\models\product\OnlineProduct::REFUND_METHOD_DEBX === $deal->refund_method) {
                $refundMethodDescription = '每月还款金额为部分本金和部分收益之和，各月还款金额相等';
            }
        ?>
        <div class="m4">还款方式：
            <span><?= Yii::$app->params['refund_method'][$deal->refund_method]?></span>
            <?php if(!empty($refundMethodDescription)) { ?>
                <img src="<?= ASSETS_BASE_URI ?>images/credit/tip.png" alt="">
            <?php }?>
        </div>


        <?php if(!empty($refundMethodDescription)) { ?>
            <div class="row" id='chart-box' hidden="true">
                <div class="col-xs-12">
                    <div>
                        <img src="<?= ASSETS_BASE_URI ?>images/credit/jiao.png" alt="">
                        <?= $refundMethodDescription?>
                    </div>
                </div>
            </div>
        <?php }?>
        <?php
            $graceDaysDescription = LoanHelper::getGraceDaysDescription($deal);
        ?>
        <?php if (!empty($graceDaysDescription)) { ?>
            <p class="notice"><?= $graceDaysDescription?></p>
        <?php } ?>
    </div>
</div>

<?php if ($deal->isFlexRate && !$deal->isRedeemable) { ?>
    <div class="row tab lee_title">
        <div class="col-xs-1"></div>
        <div class="col-xs-10 lee_shuoming">收益说明</div>
        <div class="col-xs-1"></div>
    </div>
    <div class="row message"  style="padding-bottom: 0px;" >
        <div class="col-xs-1  col-sm-1" ></div>
        <div class="col-xs-5 money col-sm-5" style="color:#9c9c9c">认购金额(元)</div>
        <div class="col-xs-5 shouyi right_num col-sm-5"  style="color:#9c9c9c;" >预期年化收益率(%)</div>
        <div class="col-xs-1 right_hide  col-sm-1" ></div>
    </div>
    <div class="row message" style="padding-bottom: 0px;" >
        <div class="col-xs-1  col-sm-1" ></div>
        <div class="col-xs-7 money col-sm-5">累计认购<?= StringUtils::amountFormat2($deal->start_money) ?>起</div>
        <div class="col-xs-3 shouyi center_num col-sm-5"><?= StringUtils::amountFormat2($deal->yield_rate * 100) ?></div>
        <div class="col-xs-1 col-sm-1" ></div>
    </div>
    <?php
        $stepArr = RateSteps::parse($deal->rateSteps);
        $count = count($stepArr);
        foreach ($stepArr as $key => $val) {
    ?>
        <div class="row message <?= $key === $count - 1 ? 'bottom-note' : '' ?>">
            <div class="col-xs-1 col-sm-1"></div>
            <div class="col-xs-7 money col-sm-5">累计认购<?= StringUtils::amountFormat2($val['min']) ?>起</div>
            <div class="col-xs-3 shouyi center_num col-sm-5"><?= StringUtils::amountFormat2($val['rate']) ?></div>
            <div class="col-xs-1 col-sm-1"></div>
        </div>
    <?php } ?>
<?php } ?>

<!-- 目前相关 -->
<!-- 添加内容开始 -->
<?php if ($deal->isRedeemable) { ?>
<div class=earn-ransom>
    <h5>收益说明</h5>
    <div class="row earnings-tips">
        <div class="col-xs-4 col">投资金额</div>
        <div class="col-xs-4 col">提前赎回利率</div>
        <div class="col-xs-4 col">到期兑付利率</div>
    </div>
    <div class="row earnings-contain">
        <div class="col-xs-4 col">认购10万(含)起</div>
        <div class="col-xs-4 col">8.6</div>
        <div class="col-xs-4 col">9.0</div>
    </div>
    <div class="row earnings-contain">
        <div class="col-xs-4 col">认购50万(含)起</div>
        <div class="col-xs-4 col">8.8</div>
        <div class="col-xs-4 col">9.3</div>
    </div>
    <div class="row earnings-contain">
        <div class="col-xs-4 col">认购100万(含)起</div>
        <div class="col-xs-4 col">9.1</div>
        <div class="col-xs-4 col">9.5</div>
    </div>
    <h5 class='ransom-h5'>赎回说明</h5>
    <ol class="ransom-list" id='ransom-list' style="list-style: decimal;">
        <li>建议投资者选择到期后自动兑付，最高利率达到9.5%；提前赎回将根据实际投资金额和投资期限计算实际收益，收益率低于到期自动兑付的利率。</li>
        <?php
            $closedExpireTime = RedeemHelper::getClosedPeriodExpireTime($deal->redemptionPeriods);
            if (null !== $closedExpireTime) {
        ?>
            <li>封闭期：本产品从购买日至<?= $closedExpireTime->format('Y年m月d日') ?>属封闭期，投资者在封闭期内不得赎回理财产品。</li>
        <?php } ?>
        <li>提前赎回：封闭期过后，投资者可在<?= RedeemHelper::formatRedemptionPeriods($deal->redemptionPeriods) ?>期间预约提前赎回；预约成功后，<?= RedeemHelper::formatRedemptionPaymentDates($deal->redemptionPaymentDates) ?>当日为投资者兑付所有投资本金及收益。赎回不收取任何手续费。</li>
        <li>到期自动兑付：如投资者不提前赎回，产品从购买日起算，满3年后，自动兑付所有投资本金及收益，收益率高于提前赎回利率。具体收益详见收益说明。</li>
        <li>付息方式说明：购买后，每自然年6月30日，12月30日按照提前赎回利率支付收益。若投资者未申请提前赎回，将在产品到期日补足所有差额收益。</li>
    </ol>
</div>
<?php } ?>
<!-- 添加内容结束 -->

<!--视频的增加-->
<?php if ($deal->issuerInfo && $deal->issuerInfo->video) { ?>
    <div class="row biaodi-video">
        <div class="play-img img-responsive center-block" id="play-img">
            <img src="<?= ASSETS_BASE_URI ?>images/credit/loading.gif">
        </div>
        <p class="video-title col-xs-12 col-sm-12 col-zero"><?= $deal->issuerInfo->mediaTitle ?></p>
        <div id="video-wrap" class="video-wrap col-xs-12 col-sm-12 col-zero">
            <video id="video" class="media video-js vjs-default-skin"
                   webkit-playsinline playsinline controls preload="none"
                   poster="<?= $deal->issuerInfo->videoImg ? UPLOAD_BASE_URI.$deal->issuerInfo->videoImg->uri : '' ?>">
                <source src="<?= $deal->issuerInfo->video->uri ?>">
            </video>
        </div>
    </div>
<?php } ?>

<div class="row tab">
    <div class="col-xs-1"></div>
    <div class="col-xs-10 tabs">
        <div class="tab1">项目详情</div>
        <div class="tab2">投资记录</div>
    </div>
    <div class="col-xs-1"></div>
</div>
<div class="row tab-conten">
    <div class="col-xs-1"></div>
    <div class="col-xs-10">
        <?= HtmlPurifier::process($deal->description) ?>
        <p style="margin-top: 1em; padding-bottom: 0.5em; font-size: 12px; color: #bababf;">*理财非存款，产品有风险，投资须谨慎</p>
    </div>
    <div class="col-xs-1"></div>
</div>
<!--投资记录-->
<div class="row touzi-box">
    <div class="col-xs-1"></div>
    <div class="col-xs-10 col">
            <div class="row touzi datafirst">
                <div class="col-xs-3 col">投资人</div>
                <div class="col-xs-5">投资时间</div>
                <div class="col-xs-4" style="padding: 0px;">投资金额(元)</div>
            </div>

        </div>
    <div class="col-xs-1"></div>
</div>

<?php if(2 === $deal->status) { ?>
    <?php if (null !== $user && $deal->is_xs && $user->xsCount() >= Yii::$app->params['xs_trade_limit']) { ?>
        <div class="row huankuang">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">您已经参与过新手专享体验</div>
            <div class="col-xs-1"></div>
        </div>
    <?php } elseif($deal->end_date >= time()) { ?>
        <form action="/deal/deal/toorder?sn=<?=$deal->sn .'&rand=' . time() . (defined('IN_APP') ? "&token=" . \yii\helpers\Html::encode(Yii::$app->request->get("token")): "") ?>" method="post" id="toorderform" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
        </form>
        <div id="x-purchase" class="row rengou" style="cursor: pointer">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">立即认购</div>
            <div class="col-xs-1"></div>
        </div>
    <?php } else { ?>
        <div class="row huankuang">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">募集结束</div>
            <div class="col-xs-1"></div>
        </div>
    <?php }?>
<?php } else { ?>
    <div class="row huankuang">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <?php
                if ($deal->status == 5 || ($deal->is_jixi && in_array($deal->status, ['3', '7']))) {
                    echo '收益中';
                } else {
                    echo Yii::$app->params['deal_status'][$deal->status];
                }
            ?>
            <?php
                if (1 === $deal->status) {
                    $start = Yii::$app->functions->getDateDesc($deal->start_date);
                    echo $start['desc'].date('H:i', $start['time']).'开始';
                }
            ?>
        </div>
        <div class="col-xs-1"></div>
    </div>
<?php } ?>

<script>
  (function() {
    if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
      handleFontSize();
    } else {
      document.addEventListener("WeixinJSBridgeReady", handleFontSize, false);
    }
    function handleFontSize() {
      // 设置网页字体为默认大小
      WeixinJSBridge.invoke('setFontSizeCallback', { 'fontSize' : 0 });
      // 重写设置网页字体大小的事件
      WeixinJSBridge.on('menu:setfont', function() {
        WeixinJSBridge.invoke('setFontSizeCallback', { 'fontSize' : 0 });
      });
    }
  })();
    <?php if ($deal->issuerInfo && $deal->issuerInfo->video) { ?>
        window.onload = function () {
            var videoDiv = document.getElementById('video');
            var playImg = document.getElementById('play-img');
            videoDiv.onclick = function() {
                if (this.paused) {
                    this.play();
                } else {
                    this.pause();
                }
            }
            videoDiv.onwaiting = function() {
                playImg.style.display = 'block';
            }
            videoDiv.oncanplay = function() {
                playImg.style.display = 'none';
            }
        }
    <?php } ?>

    $(function() {
        $('.tabs div').click(function() {
            var index = $('.tabs div').index(this);
            $('.tabs div').css({background:'#ffffff',color:'#f34334'}); //303030
            $('.tabs div').eq(index).css({background:'#f34334',color:'#ffffff'});
            if(index==1){
                $('.tab-conten').css({display:'none'});
                $('.touzi-box').css({display:'block'});
            }else{
                $('.tab-conten').css({display:'block'});
                $('.touzi-box').css({display:'none'});
            }
        })

        pid = '<?=$deal->id;?>';
        $.get('/deal/deal/orderlist',{pid:pid},function(data){
            html = "";
            for(var i=0;i<data.orders.length;i++){
                    html+='<div class="row touzi-content border-bottom1">';
                    html+='    <div class="col-xs-3 col">'+data.orders[i]['mobile']+'</div>';
                    html+='    <div class="col-xs-5 data"><span class="data1">'+data.orders[i]['time']+'</span><span class="data2">'+data.orders[i]['his']+'</span></div>';
                    html+='    <div class="col-xs-4">'+data.orders[i]['money']+'</div>';
                    html+='</div>';
            }
            $('.datafirst').after(html)
        })

    });

    function subForm2(form)
    {
        to = $(form).attr("data-to");//设置如果返回错误，是否需要跳转界面

        var xhr = $.get($(form).attr("action"), function (data) {
            if(data.code != 0 && to == 1 && data.tourl != undefined) {
                if(data.message == '请登录') {
                    toast(data.message, function() {
                        location.href = data.tourl;
                    });
                } else {
                    alertTrueVal(data.message,function(){
                        location.href = data.tourl;
                    });
                }
            } else {
                if (data.code != 0) {
                    toast(data.message);
                }

                if (to == 1 && data.tourl != undefined) {
                   location.href = data.tourl;
                }
            }
        });

        return xhr;
    }

    $('#x-purchase').on('click', function(e) {
        var $this = $(this);
        if ($this.data('x-purchase-clicked')) {
            return;
        }
        $this.data('x-purchase-clicked', true);
        var xhr = subForm2('#toorderform');
        xhr.always(function() {
            $this.data('x-purchase-clicked', false);
        });

        xhr.fail(function() {
            toast('系统繁忙，请稍后重试！');
        });
    });

    // 新的
    $('.m4 img').on('click',function(){
        $('#chart-box').stop(true,false).fadeToggle();
        $('#chart-box .col-xs-12 img').css({left:$(this).context.offsetLeft-16+"px"});
    })
</script>
