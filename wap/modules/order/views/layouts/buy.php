<?php
use yii\helpers\Html;

frontend\assets\WapAsset::register($this);
//$this->title="购买";
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no"/>
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <!--        <link href="/css/bootstrap.min.css" rel="stylesheet">-->
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <!--        <link rel="stylesheet" href="/css/index.css">-->

</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <div class="row title-box nav-height" style="margin-bottom:0;">
        <div class="col-xs-2 back"><img src="/images/back.png" alt="" onclick="history.go(-1)"/></div>
        <div class="col-xs-8 title"><?= Html::encode($this->title) ?></div>
        <div class="col-xs-2"></div>
    </div>
    <?= $content ?>

</div>


<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>



