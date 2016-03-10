<?php
use yii\helpers\Html;
use frontend\assets\WapAsset;

WapAsset::register($this);
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
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
    	<!--header-->
	<header>
             <div class="title">温都金服</div>
        </header>
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
                    <?php if (!\Yii::$app->user->isGuest) { ?>
                    <a href="/user/user"><span class="zhanghu"></span>账户</a>
                    <?php } else { ?>
                    <a href="/site/login"><span class="zhanghu"></span>账户</a>
                    <?php } ?>
                </div>
            </div>
        </div>
	</div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>



