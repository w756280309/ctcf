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
    <link rel="stylesheet" href="/css/licai.css"/>
    <link rel="stylesheet" href="/css/swiper.min.css">
    <script src="/js/jquery.js"></script>
    <script src="/js/swiper.min.js"></script>
    <script src="/js/licai.js"></script>
    <style>
        body {
            background-color: #fff;
        }
        .container-text {
            padding:4px 10px 64px;
            font-size: 12px;
            background: #fff;
            font-family:"微软雅黑", Arial
        }
        .list-txt{
            margin:1px 0 3px;
            font-size: 12px;
            color:#3e3a39;
            line-height: 18px;
        }
        h4.agree_title {
            margin:5px auto;
            text-align: center;
            font-size: 14px;
            color: #000;
            line-height: 18px;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <div class="row title-box nav-height" style="margin-bottom:0;">
        <div class="col-xs-2 back"><img src="/images/back.png" alt="" onclick="location.href='/order/order?sn=<?= $sn ?>'"/></div>
        <div class="col-xs-8 title">合同说明</div>
        <div class="col-xs-2"></div>
    </div>
    <!-- Swiper -->
    <div class="swiper-container" style="line-height: 18px;">
        <div class="swiper-wrapper" >
            <?php foreach($model as $key => $val): ?>
            <div class="swiper-slide <?= $key_f == $key?"dian":"" ?>" onclick="window.location.href='/order/order/agreement?sn=<?= $sn ?>&id=<?= $val['pid'] ?>&key=<?= $key ?>'"><?= Yii::$app->functions->cut_str($val['name'],5,0,'**') ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container-text">
        <div class="row">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">
                <h4 class="agree_title"><?= $model[$key_f]['name'] ?></h4>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="list-txt"><?= html_entity_decode($content) ?></div>
            </div>
        </div>
    </div>

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
