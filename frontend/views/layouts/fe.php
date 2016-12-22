<?php

use common\assets\FeAsset;
use common\view\AnalyticsHelper;
use yii\helpers\Html;

AnalyticsHelper::registerTo($this);
FeAsset::register($this);

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
        <?= $content ?>
    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>