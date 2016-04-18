<?php
use yii\helpers\Html;
use common\view\BaiduTongjiHelper;

frontend\assets\WapAsset::register($this);
BaiduTongjiHelper::registerTo($this, BaiduTongjiHelper::WAP_KEY);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no"/>
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

    <script src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/common.js?v=160407"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/lib.js?v=160412"></script>
    <script>
    $(function() {
        $(document).ajaxSend(function(event, jqXHR, settings) {
            var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
            if (match) {
                var val = decodeURIComponent(match[1].replace(/\+/g, " "));
                settings.url = settings.url+(settings.url.indexOf('?') >= 0 ? '&' : '?')+'token='+encodeURIComponent(val);
            }
        });
    });
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <?php if (!defined('IN_APP')) { ?>
    <?php if ($this->showAvatar) { ?>
    <!--header-->
    <div class="row account-title">
        <div class="col-xs-2 back"><img src="<?= ASSETS_BASE_URI ?>images/headpic.png" alt=""/></div>
        <div class="col-xs-8 ">ID:<?= Yii::$app->user->identity->mobile ?></div>
        <div class="col-xs-1 col"><a href="/system/system/setting" class="set">设置</a></div>
        <div class="col-xs-1"></div>
    </div>
    <?php } else { ?>
    <div class="row title-box nav-height">
        <div class="col-xs-2 back">
            <?php if (false === $this->backUrl) {} else { ?>
            <img src="<?= ASSETS_BASE_URI ?>images/back.png" alt="" onclick="<?= (null === $this->backUrl) ? 'history.go(-1)' : "window.location.href='$this->backUrl'" ?>"/>
            <?php } ?>
        </div>
        <div class="col-xs-8 title"><?= Html::encode($this->title) ?></div>
        <div class="col-xs-2"></div>
    </div>
    <?php } } ?>

    <?= $content ?>

    <!-- 添加首页页尾, 当标志位showIndexBottomNav为真时,需要显示 -->
    <!-- nav start -->
    <?php if ($this->showIndexBottomNav) { ?>
    <div class="nav-box <?= (!defined('IN_APP') && $this->showBottomNav) ? '' : 'no-margin-bottom' ?>">
        <div class="pos-rel">
            <div class="pos-fixer">
                <nav>
                    <li class="first"><a href="/">首页</a></li>
                    <li><a href="/site/about">关于我们</a></li>
                    <li><a href="/site/advantage">平台优势</a></li>
                    <li><a href="/site/help">帮助中心</a></li>
                    <li><a href="/site/contact">联系我们</a></li>
                </nav>
            </div>
        </div>
        <p>客服热线：<?= Yii::$app->params['contact_tel'] ?>（09:00-20:00）</p>
    </div>
    <?php } ?>

    <!--footer-->
    <?php if (!defined('IN_APP')) { ?>
    <?php if ($this->showBottomNav) { ?>
    <div class="row navbar-fixed-bottom footer">
        <div class="col-xs-4 footer-title">
            <div class="footer-inner">
                <a href="/" class="shouye1"><span class="shouye"></span>首页</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner1">
                <a href="/deal/deal/index"><span class="licai"></span>理财</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner2">
                <?php if (!\Yii::$app->user->isGuest) { ?>
                <a href="/user/user"><span class="zhanghu"></span>账户</a>
                <?php } else { ?>
                <a href="/site/login"><span class="zhanghu"></span>账户</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } } ?>
</div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
