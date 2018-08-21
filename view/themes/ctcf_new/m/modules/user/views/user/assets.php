<?php

use common\utils\StringUtils;
use yii\helpers\Html;

$this->title = '资产总览';

$investmentBalance = $user->lendAccount->investment_balance;
$freezeBalance = $user->lendAccount->freeze_balance;
$availableBalance = $user->lendAccount->available_balance;
$currentUrl = Yii::$app->request->absoluteUrl;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css?v=20170906">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/ucenter/myProperty.css?v=201802121">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/zepto.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/echarts.common.min.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png?v=1" alt="" onclick="location.href='/user/user'">
            <?= Html::encode($this->title) ?>
        </div>
    <?php } ?>

    <ul class="titlebar clearfix">
        <li class="f15 lf"><a class="actived" href="javascript:void(0);">我的资产</a></li>
        <li class="f15 lf"><a href="/user/user/profit">我的收益</a></li>
    </ul>
    <div class="paintersvg">
        <div id="container" style="width: 10rem; height: 7rem; margin: -0.5rem auto 0"></div>
        <img class="question" src="<?= FE_BASE_URI ?>wap/ucenter/images/question.png" alt="">
    </div>

    <div class="detialList">
        <ul>
            <li>
                <a class="clearfix slb f15" href="/user/user/myorder?back_url=<?= urlencode($currentUrl) ?>">
                    <div class="lf"><span class="colorBlock assetyellow"></span>出借资产</div>
                    <div class="rg"><span class="comColor"><?= StringUtils::amountFormat2($investmentBalance) ?></span>元</div>
                </a>
            </li>
            <li>
                <a class="clearfix slb f15" href="/user/user/myorder?type=2&back_url=<?= urlencode($currentUrl) ?>">
                    <div class="lf"><span class="colorBlock assetred f15"></span>冻结资金</div>
                    <div class="rg"><span class="comColor"><?= StringUtils::amountFormat2($freezeBalance) ?></span>元</div>
                </a>
            </li>
            <li>
                <a class="clearfix slb f15" href="/user/user/mingxi">
                    <div class="lf"><span class="colorBlock assetgreen f15"></span>可用余额</div>
                    <div class="rg"><span class="comColor"><?= StringUtils::amountFormat2($availableBalance) ?></span>元</div>
                </a>
            </li>
<!--            <li>
                <a class="clearfix slb f15" href="<?/*= $user->offline ? '/user/user/myofforder' : '#' */?>">
                    <div class="lf"><span class="colorBlock assetgreen f15"></span>门店出借</div>
                    <div class="rg"><span class="comColor"><?/*= $user->offline ? $user->offline->totalAssets : 0 */?></span>元</div>
                </a>
            </li>-->
        </ul>
    </div>
</div>

<div class="mask cancel"></div>
<div class="pomp f12">
    <p class="grey">出借资产：</p>
    <p class="black">正在出借中待回收本金总和</p>
    <p class="grey">冻结资金：</p>
    <p class="black">出借资金在项目未满标时锁定的金额</p>
    <p class="grey">可用余额：</p>
    <p class="black">当前账户可用出借、提现金额</p>
    <div class="close f15">知道了</div>
</div>
<script>
    $(function () {
        //提示信息
        $(".question").on("click",function(e){
            $('.cancel').show();
            $('.pomp').show();
        });
        $(".close,.cancel").on("click",function(e){
            if($(e.target).hasClass('close') || $(e.target).hasClass('cancel')){
                $('.cancel').hide();
                $('.pomp').hide();
            }
        });

        var myChart = echarts.init(document.getElementById('container'));
        var font , W;
        W = $(window).width();
        font = W>750?40:20;
        var option = {
            title: {
                text: '总资产',
                textStyle:{
                    color:'#999999',
                    fontSize:font,
                    fontWeight:500,
                },
                subtext: '<?= StringUtils::amountFormat3($user->totalAssets) ?>',
                subtextStyle:{
                    color:'#f25f57',
                    fontSize:font,
                    fontWeight:500,
                },
                x: 'center',
                y: '42%',
                itemGap:5,
            },
            tooltip: {
                trigger: 'item',
                formatter: function (params, ticket, callback) {
                    return params.name+'：'+WDJF.numberFormat(params.value, true);
                }
            },
            series: [
                {
                    name:'我的资产',
                    type:'pie',
                    radius: ['65%', '83%'],
                    avoidLabelOverlap: false,
                    color:["#ff5763","#ff8d39","#70ec72"],
                    label: {
                        normal: {
                            show: false
                        }
                    },
                    data:[
                        {value: <?= $freezeBalance ?>, name:'冻结资金'},
                        {value: <?= $investmentBalance ?>, name:'出借资产'},
                        {value: <?= $availableBalance ?>, name:'可用余额'}
                    ]
                },
                {
                    type:'pie',
                    radius: ['83%', '90%'],
                    avoidLabelOverlap: false,
                    tooltip: {
                        show:false
                    },
                    itemStyle: {
                        normal: {
                            color: '#fbcfcc'
                        },
                        emphasis: {
                            color: '#fbcfcc'
                        }
                    },
                    label: {
                        normal: {
                            show: false
                        }
                    },
                    data: [3000000, 1]
                }
            ]
        };
        myChart.setOption(option);
    })
</script>
