<?php

use common\view\AnalyticsHelper;
use yii\helpers\Html;

frontend\assets\FrontAsset::register($this);

AnalyticsHelper::registerTo($this);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Yii::$app->params['pc_page_keywords'].','.trim($this->extraKeywords, ', '),
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::$app->params['pc_page_desc'],
]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= Html::encode($this->title).' - 温都金服' ?></title>
        <?php $this->head() ?>
        <meta name="baidu-site-verification" content="7YkufMc2UW" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    </head>

    <body>
        <?php $this->beginBody() ?>
            <?= $this->render('@frontend/views/header.php'); ?>

            <?= $content ?>

            <?= $this->render('@frontend/views/footer.php'); ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>