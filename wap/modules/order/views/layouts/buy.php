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
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
    <?php if (!defined('IN_APP')) { ?>
    <div class="row title-box nav-height">
        <div class="col-xs-2 back">
            <?php if ('order' !== Yii::$app->controller->id && 'ordererror' !== Yii::$app->controller->action->id) { ?>
                <img src="<?= ASSETS_BASE_URI ?>images/back.png" alt="" onclick="history.go(-1)"/>
            <?php } ?>
        </div>
        <div class="col-xs-8 title"><?= Html::encode($this->title) ?></div>
        <div class="col-xs-2"></div>
    </div>
    <?php } ?>
    <?= $content ?>
</div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>