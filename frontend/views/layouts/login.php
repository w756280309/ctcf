<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\view\AnalyticsHelper;

AppAsset::register($this);
AnalyticsHelper::registerTo($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <meta name="baidu-site-verification" content="7YkufMc2UW" />
        <link rel="stylesheet" href="<?=ASSETS_BASE_URI ?>css/index.css?v=20160405">
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

    <body class="page-login">
        <?php $this->beginBody() ?>
        <?= $this->render('@frontend/views/site/header.php'); ?>
        <?= $content ?>
        <?= $this->render('@frontend/views/site/footer.php'); ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
