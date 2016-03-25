<?php
use yii\helpers\Html;
use frontend\assets\WapAsset;
use common\view\BaiduTongjiHelper;

WapAsset::register($this);

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
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
        <?php if (!\Yii::$app->request->get('in_app')) { ?>
    	<!--header-->
	<header>
             <div class="title">温都金服</div>
        </header>
        <?php } ?>

        <?= $content ?>

        <!--footer-->
        <?php if (!\Yii::$app->request->get('in_app')) { ?>
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
        <?php } ?>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>



