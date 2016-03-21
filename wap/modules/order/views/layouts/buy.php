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
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "//hm.baidu.com/hm.js?d2417f8d221ffd4b883d5e257e21736c";
          var s = document.getElementsByTagName("script")[0];
          s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <div class="row title-box nav-height" style="margin-bottom:0;">
        <div class="col-xs-2 back"><img src="<?= ASSETS_BASE_URI ?>images/back.png" alt="" onclick="history.go(-1)"/></div>
        <div class="col-xs-8 title"><?= Html::encode($this->title) ?></div>
        <div class="col-xs-2"></div>
    </div>
    <?= $content ?>
</div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>



