<?php
$this->title = '邀请好友来挣钱，大把红包轻松拿';

use yii\helpers\Html;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/invite/invite.css?v=20161208">

<div class="wdjf-invite">
    <div class="banner banner1"></div>
    <div class="banner banner2"></div>
    <div class="banner banner3"></div>
    <div class="banner banner4"></div>
    <div class="wdjf-ucenter">
        <!-- 被邀请人奖励 -->
        <div class="invite ">
            <div class="title first">
                邀请人奖励
                <img class="title-lf" src="<?= ASSETS_BASE_URI ?>images/invite/title-lf.png">
                <img class="title-rg" src="<?= ASSETS_BASE_URI ?>images/invite/title-rg.png">
            </div>
            <div class="box">
                <div class="box-ticket ">
                    <div class="ticket ticket-left">
                        <p class="num"><span>30</span>元</p>
                        <p class="coupon">(投资代金券)</p>
                        <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/invite/border-fff.png">
                    </div>
                    <p class="ticket-txt">好友首次投资<10,000元</p>
                </div>
                <div class="box-ticket marg-lf">
                    <div class="ticket ticket-center">
                        <p class="num"><span>50</span>元</p>
                        <p class="coupon">(投资代金券)</p>
                        <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/invite/border-fff.png">
                    </div>
                    <p class="ticket-txt">好友首次投资>=10,000元</p>
                </div>
                <div class="box-ticket marg-lf">
                    <div class="ticket ticket-right">
                        <p class="num"><img src="<?= ASSETS_BASE_URI ?>images/invite/money.png"></p>
                        <p class="coupon">(现金红包)</p>
                        <img class="border-fff" src="<?= ASSETS_BASE_URI ?>images/invite/border-fff.png">
                    </div>
                    <p class="ticket-txt">好友前三次投资的0.1%</p>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>

        <!-- 邀请人奖励 -->
        <div class="invite ">
            <div class="title second">
                被邀请人奖励
                <img class="title-lf" src="<?= ASSETS_BASE_URI ?>images/invite/title-lf.png">
                <img class="title-rg" src="<?= ASSETS_BASE_URI ?>images/invite/title-rg.png">
            </div>
            <div class="box">
                <div class="zhuce">
                    <div class="ticket-pos">
                        <p class="num"><span>50</span>元</p>
                        <p class="coupon">(投资代金券)</p>
                    </div>
                    <p class="zhuce-bottom">好友注册可得</p>
                </div>
            </div>
        </div>

        <!-- 规则 -->
        <div class="invite rule">
            <div class="title three">
                活动规则
                <img class="title-lf" src="<?= ASSETS_BASE_URI ?>images/invite/title-lf.png">
                <img class="title-rg" src="<?= ASSETS_BASE_URI ?>images/invite/title-rg.png">
            </div>
            <div class="box">
                <p><span>活动规则：</span></p>
                <p>1. 登录温都金服网站，进入“我的账户”；</p>
                <p>2. 点击“邀请好友”可以看到邀请好友活动，通过微信或者链接进行邀请；</p>
                <p>3. 当您的小伙伴通过此邀请链接注册并成功投资后，您即可获得邀请好友的奖励；</p>
                <p>4. 邀请人在邀请好友之前必须在平台投资过，有投资记录才能参与现金返现活动，发放奖励现金时，以"角"为单位取整，采用四舍五入；</p>
                <p>5. 新手专享标和转让均不参加邀请奖励；</p>
                <p>6. 严禁恶意刷邀请好友，如有发生，封号处理。</p>
                <br/>
                <p><span>奖励规则：</span></p>
                <p>1. 被邀请好友首次单笔投资（新手专享和转让除外）1万元以上（含1万元），邀请人获得1张50元代金券；</p>
                <p>2. 被邀请好友首次单笔投资（新手专享和转让除外）1万元以下（不含1万元），邀请人获得1张30元代金券；</p>
                <p>3. 邀请人获得被邀请人投资额0.1% 的奖励返现（仅限前三次投资，新手专享和转让除外）；</p>
                <p>4. 被邀请人注册即可获得50元代金券。</p>
                <br/>
                <p><span>代金券使用规则：</span></p>
                <p>1. 代金券有效期30天（单笔投资满1万元抵扣）。</p>
            </div>
        </div>

        <!-- 邀请好友按钮 -->
        <?php if (\Yii::$app->user->isGuest) {  ?>
            <a href="/site/login?next=<?= Html::encode(\Yii::$app->request->hostInfo.'/user/invite') ?>" class="a-invite-btn">邀请好友</a>
        <?php } else { ?>
            <a href="/user/invite" class="a-invite-btn">邀请好友</a>
        <?php } ?>
        <p class="invite-tips">理财非存款，产品有风险，投资须谨慎</p>
    </div>
</div>