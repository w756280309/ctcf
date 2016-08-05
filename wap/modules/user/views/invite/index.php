<?php
$this->title = '邀请好友';

$this->registerJsFile(ASSETS_BASE_URI . 'js/invite/invite.js?v=20160802', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI . 'css/invite/invite.css?v=20160803', ['depends' => 'wap\assets\WapAsset']);
$this->registerJs('var cdn = \'' . (ASSETS_BASE_URI === '/' ? \Yii::$app->request->hostInfo . '/' : ASSETS_BASE_URI) . '\';', 1);
$this->registerJs('var invite_url = \'' . \Yii::$app->request->hostInfo . '/luodiye/invite?code=' . $user->usercode . '\';', 1);
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
$this->registerJsFile(ASSETS_BASE_URI . 'promo/1608/js/weixin.js?v=20160805');//加载来源统计记录代码

use common\utils\StringUtils;
?>
<script type="text/javascript">
    var url = '/user/invite';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<!--banner-box-->
<div style="clear: both;"></div>
<div class="row banner-box">
    <div class="col-xs-12">
        <img src="<?= ASSETS_BASE_URI ?>images/invite/banner1.png" alt="">
        <img src="<?= ASSETS_BASE_URI ?>images/invite/banner2.png" alt="">
    </div>
</div>
<!--invite-box-->
<div class="row invite-box">
    <div class="col-xs-12">
        <div class="inv-title">
            <span><img class="left" src="<?= ASSETS_BASE_URI ?>images/invite/left.png" alt="">邀请人奖励<img class="right" src="<?= ASSETS_BASE_URI ?>images/invite/right.png" alt=""></span>
        </div>
        <ul class="invite-inner clearfix">
            <li>
                <div class="invite-quan background-yellow">
                    <div>
                        <p>30<i>元</i></p>
                        <span>(投资代金券)</span>
                    </div>
                </div>
                <em>好友首次投资<br/><10,000元</em>
            </li>
            <li>
                <div class="invite-quan background-orange">
                    <div>
                        <p>50<i>元</i></p>
                        <span>(投资代金券)</span>
                    </div>
                </div>
                <em>好友首次投资<br/>>=10,000元</em>
            </li>
            <li>
                <div class="invite-quan background-red">
                    <div>
                        <p><img src="<?= ASSETS_BASE_URI ?>images/invite/hongbao.png" alt=""></p>
                        <span>(现金红包)</span>
                    </div>
                </div>
                <em>好友前三次<br/>投资的0.1%</em>
            </li>
        </ul>
    </div>
</div>
<!--invited-box-->
<div class="row invited-box">
    <div class="col-xs-12">
        <div class="inv-title">
            <span><img class="left" src="<?= ASSETS_BASE_URI ?>images/invite/left.png" alt="">被邀请人奖励<img class="right" src="<?= ASSETS_BASE_URI ?>images/invite/right.png" alt=""></span>
        </div>
        <ul class="invited-inner clearfix">
            <li>
                <div class="invited-quan">

                </div>
            </li>
            <li>
                <div class="invited-content">
                    <div class="invited-quan background-red invited-quans">
                        <div>
                            <p>30<i>元</i></p>
                            <span>(投资代金券)</span>
                        </div>
                    </div>
                    <div class="invited-quan background-red invited-quans1">
                        <div>
                            <p>50<i>元</i></p>
                            <span>(投资代金券)</span>
                        </div>
                    </div>
                </div>
                <em>好友注册即得</em>
            </li>
            <li>
                <div class="invited-quan"></div>
            </li>
        </ul>
    </div>
</div>
<!--middle-box-->
<div class="row middle-box">
    <div class="col-xs-12">
        <div class="inv-title">邀请越多好友，奖励越多，最高奖励无上限</div>
        <ul class="middle-inner clearfix">
            <li>邀请人数：<span><?= count($model) ?></span>个 <i>代金券奖励：<span><?= StringUtils::amountFormat2(array_sum(array_column($model, 'coupon'))) ?></span>元</i></li>
            <li>现金红包奖励：<span><?= StringUtils::amountFormat3(array_sum(array_column($model, 'cash'))) ?></span>元</li>
        </ul>
    </div>
</div>
<!--bottom-box-->
<div class="row bottom-box">
    <div  class="col-xs-12">
        <div class="title-box">
            <div class="inv-title clearfix">
                <!--被选中添加selected类-->
                <span class="selected">邀请列表</span>
                <span>活动规则</span>
            </div>
        </div>
        <!--邀请列表-->
        <div class="invite-list">
            <table class="bottom-inner">
                <tr>
                    <th>姓名</th>
                    <th>注册日期</th>
                    <th>代金券(元)</th>
                    <th>现金红包(元)</th>
                </tr>
                <?= $this->renderFile('@wap/modules/user/views/invite/list.php',['data' => $data])?>
                <tr class="load"></tr>
            </table>

            <?php if (empty($model)) : ?>
                <div class="no-data">暂未获得邀请奖励，快前去邀请吧</div>
            <?php endif; ?>
        </div>

        <!--活动规则-->
        <div class="rule-box">
            <div class="inv-title"><b>活动时间：</b>2016年8月4日~2016年9月25日</div>
            <div class="inv-title"><b>活动规则：</b></div>
            <ul>
                <li>登录温都金服网站，进入“我的账户”；</li>
                <li>点击“邀请好友”可以看到邀请好友活动，通过微信或者链接进行邀请；</li>
                <li>当您的小伙伴通过此邀请链接注册并成功投资后，您即可获得邀请好友的奖励；</li>
                <li>现金奖励需要您有投资记录才能发放，发放奖励现金时，以"角"为单位取整，采用四舍五入；</li>
                <li>严禁恶意刷邀请好友，如有发生，封号处理。</li>
            </ul>
            <div class="inv-title"><b>奖励规则：</b></div>
            <ul>
                <li>被邀请好友首次单笔投资1万元以上（含1万元），邀请人获得1张50元代金券；</li>
                <li>被邀请好友首次单笔投资1万元以下（不含1万元），邀请人获得1张30元代金券；</li>
                <li>邀请人获得被邀请人投资额0.1% 的奖励返现（仅限前三次投资）；</li>
                <li>被邀请人注册即可获得50元代金券。</li>
            </ul>
            <div class="inv-title"><b>代金券使用规则：</b></div>
            <ul>
                <li>代金券有效期30天(单笔投资满1万元抵扣)。</li>
            </ul>
        </div>
    </div>
</div>
<!--invite-btn-->
<div class="invite-btn">邀请好友</div>
<!--share-box-->
<div class="mark-box"></div>
<div class="share-box">
    <img src="<?= ASSETS_BASE_URI ?>images/invite/share.png" alt="">
</div>
