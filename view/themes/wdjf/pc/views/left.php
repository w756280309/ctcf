<?php

use frontend\assets\FrontAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/left.css', ['depends' => FrontAsset::class]);
$action = Yii::$app->controller->action->getUniqueId();
$user = Yii::$app->user->getIdentity();
?>

<div class="userAccount-left-nav">
    <ul>
        <li class="nav-head"><span>账户中心</span></li>
        <li class="nav-title"><span>我的账户</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'user/user/index' === $action ? 'selected' : '' ?>"><a href="/user/user"><span class="star"></span>资产总览</a></li>
                <li class="recharge <?= ('user/userbank/recharge' == $action || 'user/recharge/init' == $action) ? 'selected' : '' ?>"><a href="/user/recharge/init"><span class="star"></span>充值</a></li>
                <li class="draw <?= ('user/draw/tixian' == $action) ? 'selected' : '' ?>"><a href="/user/draw/tixian"><span class="star"></span>提现</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>资产管理</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'user/user/myorder' === $action ? 'selected' : '' ?>"><a href="/user/user/myorder"><span class="star"></span>我的理财</a></li>
                <?php if (
                        Yii::$app->params['feature_credit_note_on']
                        && !empty($user)
                        && ($user->orderCount() > 0 || $user->creditOrderCount() > 0)
                ) {  ?>
                    <li class="<?= in_array($action, ['credit/trade/assets', 'credit/note/new']) ? 'selected' : '' ?>"><a href="/credit/trade/assets"><span class="star"></span>我的转让</a></li>
                <?php } ?>
                <li class="<?= 'user/coupon/index' === $action ? 'selected' : '' ?>"><a href="/user/coupon/"><span class="star"></span>我的代金券</a></li>
                <li class="<?= in_array($action, ['mall/point/index', 'mall/point/rules']) ? 'selected' : '' ?>"><a href="/mall/point"><span class="star"></span>我的积分</a></li>
                <li class="<?= 'user/invite/index' === $action ? 'selected' : '' ?>"><a href="/user/invite/"><span class="star"></span>邀请好友</a></li>
                <li class="<?= 'user/user/mingxi' === $action ? 'selected' : '' ?>"><a href="/user/user/mingxi"><span class="star"></span>交易明细</a></li>
                <li class="<?= 'user/user/myofforder' === $action ? 'selected' : '' ?>"><a href="/user/user/myofforder"><span class="star"></span>门店理财</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>账户管理</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= in_array($action, [
                    'user/bank/card',
                    'user/bank/index',
                    'user/bank/update',
                    'user/userbank/xiane',
                ]) ? 'selected' : '' ?> mycard"><a href="/user/bank/card"><span class="star"></span>我的银行卡</a></li>
                <li class="<?= in_array($action, [
                    'user/securitycenter/index',
                ])   ? 'selected' : '' ?>"><a href="/user/securitycenter/"><span class="star"></span>安全中心</a></li>
                <li class="account <?= in_array($action, [
                    'user/identity/index',
                    'user/userbank/identity'
                ]) ? 'selected' : '' ?>"><a href="/user/userbank/identity?from=<?= urlencode(Yii::$app->request->hostInfo.'/user/user') ?>"><span class="star"></span>资金托管账户</a></li>
                <li class="<?= in_array($action, [
                    'risk/risk/index',
                ]) ? 'selected' : '' ?>"><a href="/risk/risk"><span class="star"></span>风险测评</a></li>
                <?php if (!is_null($user) && $user->isShowNjq) { ?>
                    <li>
                        <a href="/njq/connect?redirect=<?= urlencode('user/user?utm_source='.$user->campaign_source) ?>" target="_blank"><span class="star"></span>南金账户中心</a>
                    </li>
                <?php } ?>
            </ul>
        </li>
    </ul>
</div>
