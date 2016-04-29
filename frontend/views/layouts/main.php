<?php
use yii\helpers\Html;
use common\view\BaiduTongjiHelper;

BaiduTongjiHelper::registerTo($this, BaiduTongjiHelper::PC_KEY);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <meta name="baidu-site-verification" content="7YkufMc2UW" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="<?=ASSETS_BASE_URI ?>css/jquery.fullPage.css">
        <link rel="stylesheet" href="<?=ASSETS_BASE_URI ?>css/jquery.fullPage.extend.css">
        <link rel="stylesheet" href="<?=ASSETS_BASE_URI ?>css/index.css?v=20160410">
        <script src="<?=ASSETS_BASE_URI ?>js/jquery-1.8.3.min.js"></script>
        <script src="<?=ASSETS_BASE_URI ?>js/jquery-ui.min.js"></script>
        <script src="<?=ASSETS_BASE_URI ?>js/jquery.fullPage.min.js"></script>
        <script src="<?=ASSETS_BASE_URI ?>js/jquery.fullPage.extend.js"></script>
        <script src="<?=ASSETS_BASE_URI ?>js/index.js?v=20160428"></script>
        <!--[if lt IE 9]>
        <style>
            .section1 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section2 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section3 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
            .section4 {
                -ms-behavior: url(css/backgroundsize.min.htc);
                behavior: url(css/backgroundsize.min.htc);
            }
        </style>
        <![endif]-->
    </head>

    <body>
        <?php $this->beginBody() ?>
        <?= $this->render('@frontend/views/site/header.php'); ?>
        <div id="box">
            <?= $content ?>
            <?= $this->render('@frontend/views/site/footer.php'); ?>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
