<?php
use yii\helpers\Html;
frontend\assets\WapAsset::register($this);
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
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
    <script>
        $(function(){
            $('.footer-inner a').css({color: '#8c8c8c'});
            $('.footer-inner2 a').css({color: '#f44336'});
            $('.footer-inner1 a').css({color: '#8c8c8c'});
            $('.footer-inner1 .licai').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -113px -3px',backgroundSize: '200px'});
            $('.footer-inner2 .zhanghu').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -81px -57px',backgroundSize: '200px'});
            $('.footer-inner .shouye').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -145px -3px',backgroundSize: '200px'});
        })
    </script>
</head>
<body style='margin-bottom:90px'>
    <?php $this->beginBody() ?>
    <div class="container">
        <!--header-->
        <div class="row account-title">
            <div class="col-xs-2 back"><img src="<?= ASSETS_BASE_URI ?>images/headpic.png" alt=""/></div>
            <div class="col-xs-8 ">ID:<?= Yii::$app->user->identity->mobile ?></div>
            <div class="col-xs-1 col"><a href="/system/system/setting" class="set">设置</a></div>
            <div class="col-xs-1"></div>
        </div>

        <?= $content ?>

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
                    <?php if(!\Yii::$app->user->isGuest) { ?>
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



