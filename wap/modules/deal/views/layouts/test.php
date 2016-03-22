<?php
use yii\helpers\Html;
use common\view\BaiduTongjiHelper;

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
    <title>温都金服</title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/index.css"/>
    <script src="<?= ASSETS_BASE_URI ?>js/TouchSlide.1.1.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/list.js"></script>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
        <!--header-->
        <div class="row licai-list">
            <div class="col-xs-12 title">理财列表</div>
        </div>
        <?= $content ?>

        <!--footer-->
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
                    <a href="/user/user/index"><span class="zhanghu"></span>账户</a>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>



