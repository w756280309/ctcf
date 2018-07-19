<?php

use frontend\assets\FrontAsset;

$this->title = '积分规则';

$this->registerCssFile(FE_BASE_URI.'pc/common/css/base.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(FE_BASE_URI.'pc/my_point/css/index_2.css?v=20170411', ['depends' => FrontAsset::class]);

?>

<div class="point_rule">
    <div class="rule_title">积分规则</div>
    <div class="rule_content">
        <p class="content_top">楚天财富积分是基于用户回馈计划，为答谢用户长期以来的支持与厚爱而特别推出的一项增值服务。楚天财富用户可在积分商城自助查询积分数量，使用积分兑换商品，参与积分活动等。</p>
        <div class="item_box">
            <p class="box_title">
                <img src="<?= FE_BASE_URI ?>pc/my_point/images/ruleicon_1.png" alt="">怎么获得积分
            </p>
            <ul class="box_content">
                <li>出借产品：</li>
                <li>出借楚天财富产品（新手专享标、转让的项目不参加）均会产生积分：</li>
                <li>1. 当理财年化金额达到1000元可累计6分，即积分=年化出借金额*6/1000，不足1积分的按1积分计算；</li>
                <li>2. 出借产品后，相应的积分将会打入您的积分账户中；</li>
                <li>3. 年化出借额是根据您出借项目的期限来计算的，年化出借额=出借金额*项目期限/365。</li>
                <li>签到得积分：</li>
                <li>在楚天财富移动端首页点击“签到有礼”进入签到页面完成每日签到任务，即可获得签到积分。</li>
            </ul>
        </div>
        <div class="item_box">
            <p class="box_title">
                <img src="<?= FE_BASE_URI ?>pc/my_point/images/ruleicon_2.png" alt="">如何查询我的积分
            </p>
            <ul class="box_content">
                <li>方法一：您可以登录楚天财富平台查询，在"我的账户-积分商城"中，可查看您当前的线上积分数量；</li>
                <li>方法二：线下客户拨打客服热线<?= Yii::$app->params['platform_info.contact_tel'] ?>查询您当前个人的积分数量。</li>
            </ul>
        </div>
        <div class="item_box">
            <p class="box_title">
                <img src="<?= FE_BASE_URI ?>pc/my_point/images/ruleicon_3.png" alt="">积分如何换购商品
            </p>
            <ul class="box_content">
                <li>1. 登录楚天财富平台进入"积分商城"，选择喜爱的礼品进行兑换；</li>
                <li>2. 线下客户通过线下服务点申请办理兑换。</li>
                <li>礼品一旦兑换成功不能更改，积分兑换礼品后请在30天内前来领取，逾期作废，如有问题请联系客服人员：<?= Yii::$app->params['platform_info.contact_tel'] ?></li>
                <li>不同账户积分不可合并使用</li>
            </ul>
        </div>
        <div class="item_box">
            <p class="box_title">
                <img src="<?= FE_BASE_URI ?>pc/my_point/images/ruleicon_4.png" alt="">如何查询已兑换的商品订单
            </p>
            <ul class="box_content">
                <li>登录楚天财富平台，进入"我的账户"-"积分商城"-"已兑换物品"即可查看。</li>
            </ul>
        </div>
        <div class="item_box">
            <p class="box_title">
                <img src="<?= FE_BASE_URI ?>pc/my_point/images/ruleicon_5.png" alt="">积分活动方式
            </p>
            <ul class="box_content">
                <li>积分兑换：积分兑换奖品定期投放，每次礼品投放均有一定数量，兑完为止。</li>
                <li>未兑换的积分永久有效，没有过期时间。</li>
            </ul>
        </div>
    </div>
    <span class="zhushi">*积分商城最终解释权归楚天财富所有</span>
</div>