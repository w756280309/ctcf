<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\view\BaiduTongjiHelper;

AppAsset::register($this);
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
        <link rel="stylesheet" href="/css/index.css">
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
