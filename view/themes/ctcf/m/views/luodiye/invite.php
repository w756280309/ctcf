<?php

use common\models\adv\Share;
use common\view\WxshareHelper;
use wap\assets\WapAsset;

$this->title = "湖北日报新媒体集团旗下理财平台";
$this->params['breadcrumbs'][] = $this->title;
$this->headerNavOn = true;

$this->registerCssFile(ASSETS_BASE_URI.'css/first.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/invite/activedisplay.css?v=20160810', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/invite/activedisplay.js?v=20161013', ['depends' => WapAsset::class]);

if ($isLuodiye) {
    $host = Yii::$app->params['clientOption']['host']['wap'];

    $share = new Share([
        'title' => '送你一个红包，快打开看看！',
        'description' => '湖北日报新媒体集团旗下理财平台，国资背景，稳健好收益。',
        'url' => $host.'luodiye',
        'imgUrl' => ('/' === ASSETS_BASE_URI ? $host : ASSETS_BASE_URI).'images/wechat/0926.jpg',
    ]);

    WxshareHelper::registerTo($this, $share);
}

?>

<div class="row transform-box">
    <div class="col-xs-12 transform-front-box">
        <div class="logo-back">
            <img src="<?= ASSETS_BASE_URI ?>images/ctcf/logo_back.jpg?v=1" alt="">
        </div>
        <div class="front-font">
            <?php if ($isLuodiye) { ?>
                <p>新手标288元红包等你拿</p>
                <p>预期年化收益率10%</p>
            <?php } else { ?>
                <p>好友邀请送好礼</p>
                <p>快来领取吧！</p>
            <?php } ?>
        </div>
        <div class="transform-rotate">
            <img src="<?= ASSETS_BASE_URI ?>images/invite/transform-back.png" alt="">
            <div class="transform-absolute">
                <img id="transform-icon" src="<?= ASSETS_BASE_URI ?>images/invite/transform-icon.png" alt="">
            </div>
        </div>
    </div>
    <div class="col-xs-12 transform-back-box">
        <div class="transform-top">
            <img src="<?= ASSETS_BASE_URI ?>images/invite/transform-complete.png" alt="">
        </div>
        <div class="transform-center">
            <p class="p-tip">恭喜您获得代金券！</p>
            <p class="p-number"><?= $isLuodiye ? 288 : 50 ?>元</p>
            <p class="p-use">投资即可使用</p>
        </div>
        <div class="transform-bottom">
            <a href="/luodiye/signup/" id="collect">立即领取</a>
        </div>
    </div>
</div>
<div class="row description-box">
    <p class="description-header"><span>什么是楚天财富？</span></p>
    <p class="description-content">楚天财富（武汉）金融服务有限公司简称“楚天财富”，隶属湖北日报新媒体集团旗下的理财平台。甄选各类金融机构、优质企业理财产品。提供银行级理财服务，保障用户资金，安享稳健收益。</p>
</div>
<div class="row choose-box">
    <h3>为什么选择楚天财富？</h3>
    <div class="choose-content">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/choose-top.png" alt="">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/why-wdjf-new.png" alt="">
    </div>
</div>
<div class="back-fff">
    <a class="link-last" href="/deal/deal/index/">立即认购</a>
</div>
<p class="danger-tip">理财非存款，产品有风险，投资须谨慎</p>
