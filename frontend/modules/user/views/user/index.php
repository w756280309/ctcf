<?php
$this->title = '账户中心首页';

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/index.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/highcharts.js', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/index.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\models\order\OnlineRepaymentPlan as Plan;
use common\utils\StringUtils;
?>

<!--账户总资产-->
<input id="property-value" type="hidden" value="<?= StringUtils::amountFormat3($user->lendAccount->totalFund) ?>">
<!--可用余额-->
<input id="AvlBal" type="hidden" value="<?= $user->lendAccount->available_balance ?>">
<!--理财资产-->
<input id="DaishouBenjin" type="hidden" value="<?= $user->lendAccount->investment_balance ?>">
<!--冻结资金-->
<input id="FrzBal" type="hidden" value="<?= $user->lendAccount->freeze_balance ?>">

<div class="top-box">
    <div class="top-box-top">
        <ul class="top-inner clearfix">
            <li class="name blackFont">欢迎您，<?= StringUtils::obfsMobileNumber($user->mobile) ?></li>
            <li style="margin-left: 19px;"><a href="/user/securitycenter/"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/pass<?= $user->idcard_status ?>.png" alt=""></a></li>
            <li><a href="/user/securitycenter/"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/phone1.png" alt=""></a></li>
            <li><a href="/user/securitycenter/"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/card<?= $user->isQpayEnabled() ? 0 : 1 ?>.png" alt=""></a></li>
        </ul>
        <a href="/user/recharge/init" class="recharge-btn redBtnBg">充值</a>
        <a href="/user/draw/tixian" class="tixian-btn redBtnBg">提现</a>
    </div>
    <div class="top-box-bottom">
        <ul class="clearfix">
            <li class="grayFont">累计投资：<span class="redFont"><?= StringUtils::amountFormat3($user->getTotalInvestment()) ?></span> <i class="blackFont">元</i><img id="tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt=""><div class="grayFont dialog">投资金额累计相加</div></li>
            <li class="grayFont">累计收益：<span class="redFont"><?= StringUtils::amountFormat3($user->getProfit()) ?></span> <i class="blackFont">元</i></li>
        </ul>
    </div>
</div>
<!--property-box-->
<div class="property-box">
    <div class="property-top">
        <div class="property-top-logo"></div>
        <div class="property-top-content blackFont">我的资产</div>
    </div>
    <div class="property-bottom">
        <div class="property-tip-show grayFont">账户总资产=可用余额+理财资产+冻结资金 <img class="dialog-jiao" src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao.png" alt=""></div>
        <img class="property-tip" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
        <div class="property-bottom-left" id="container">
        </div>
        <div class="property-bottom-right">
            <ul>
                <li class="grayFont">
                    <div class="property-point orangeBgdeep"></div>
                    <i>可用余额</i>
                    <img class="tips" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                    <i class="redFont"><?= StringUtils::amountFormat3($user->lendAccount->available_balance) ?></i>元
                    <div class="property-tishi" style="left: -2px;">当前账户可用投资，提现金额 <img class="dialog-jiao" src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao.png" alt=""></div>
                </li>
                <li class="grayFont">
                    <div class="property-point greenBg"></div>
                    <i>理财资产</i>
                    <img class="tips" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                    <i class="redFont"><?= StringUtils::amountFormat3($user->lendAccount->investment_balance) ?></i>元
                    <div class="property-tishi" style="left: 3px;">正在投资中待回收本金总和 <img class="dialog-jiao" src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao.png" alt=""></div>
                </li>
                <li class="grayFont">
                    <div class="property-point redBg"></div>
                    <i>冻结资金</i>
                    <img class="tips" src="<?= ASSETS_BASE_URI ?>images/useraccount/tip.png" alt="">
                    <i class="redFont"><?= StringUtils::amountFormat3($user->lendAccount->freeze_balance) ?></i>元
                    <div class="property-tishi" style="left: -20px;">投资资金在项目未满标时锁定的金额<img class="dialog-jiao" src="<?= ASSETS_BASE_URI ?>images/useraccount/diglog-jiao.png" alt=""></div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--investment-box-->
<div class="investment-box">
    <div class="investment-box-top">
        <div class="investment-box-logo"></div>
        <div class="investment-box-content blackFont">在投项目</div>
        <div class="investment-link"><a href="/licai/"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/jiantou.png" alt=""></a></div>
    </div>
    <div class="investment-box-bottom">
        <div class="investment-bottom-title">
            <ul>
                <li class="investment-name"><div style="padding-left: 10px;">项目名称</div></li>
                <li class="investment-money"><div>投资金额(元)</div></li>
                <li class="investment-profit"><div>预期收益(元)</div></li>
                <li class="investment-time"><div>项目期限</div></li>
                <li class="investment-contract"><div>状态</div></li>
            </ul>
        </div>
        <?php if ($orders) { ?>
            <ul class="investment-bottom-content">
                <?php foreach($orders as $model) : ?>
                    <li  class="clearfix">
                        <div class="investment-inner investment-inner1"><div class="investment-name grayFont investment-box-vertical" style="text-align: left;"><div class="investment-name1"><a href="/deal/deal/detail?sn=<?= $model->loan->sn ?>"><?= $model->loan->title ?></a></div></div></div>
                        <div class="investment-inner"><div class="investment-money grayFont investment-box-vertical"><i><?= StringUtils::amountFormat3($model->order_money) ?></i></div></div>
                        <div class="investment-inner"><div class="investment-profit grayFont investment-box-vertical"><i><?= StringUtils::amountFormat3(Plan::getTotalLixi($model->loan, $model)) ?></i></div></div>
                        <div class="investment-inner"><div class="investment-time grayFont investment-box-vertical"><i><?= $model->loan->expires.(1 === $model->loan->refund_method ? "天" : "个月") ?></i></div></div>
                        <div class="investment-inner"><div class="investment-state grayFont investment-box-vertical"><i><?= \Yii::$app->params['deal_status'][$model->loan->status] ?></i></div></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php } else { ?>
            <!--无数据是显示-->
            <p class="not_yet">暂无投资项目<a href="/licai/">立即投资</a></p>
        <?php } ?>
    </div>
</div>
