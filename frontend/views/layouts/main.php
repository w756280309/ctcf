<?php

use common\models\growth\PageMeta;
use common\view\AnalyticsHelper;
use frontend\assets\FrontAsset;
use yii\helpers\Html;

FrontAsset::register($this);
AnalyticsHelper::registerTo($this);

$meta = PageMeta::getMeta(Yii::$app->request->absoluteUrl);

if (null !== $meta) {
    $keywords = Html::encode($meta->keywords);
    $ctitle = Html::encode($meta->title);
    $description = Html::encode($meta->description);
} else {
    $keywords = Yii::$app->params['pc_page_keywords'].','.trim($this->extraKeywords, ', ');
    $description = Yii::$app->params['pc_page_desc'];
}

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $keywords,
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= isset($ctitle) ? $ctitle : Html::encode($this->title).' - 温都金服' ?></title>
        <?php $this->head() ?>
        <meta name="baidu-site-verification" content="7YkufMc2UW" />
        <meta name="sogou_site_verification" content="2hsKBLBEiz" />
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
