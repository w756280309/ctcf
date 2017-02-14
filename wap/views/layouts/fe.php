<?php

use common\assets\FeAsset;
use common\models\growth\PageMeta;
use common\view\AnalyticsHelper;
use common\view\WxshareHelper;
use yii\helpers\Html;

FeAsset::register($this);
AnalyticsHelper::registerTo($this);
WxshareHelper::registerTo($this, $this->share);

$meta = PageMeta::getMeta(Yii::$app->request);

if (null !== $meta) {
    $keywords = Html::encode($meta->keywords);
    $ctitle = Html::encode($meta->title);
    $description = Html::encode($meta->description);
} else {
    $keywords = Yii::$app->params['wap_page_keywords'].','.trim($this->extraKeywords, ', ');
    $description = Yii::$app->params['wap_page_descritpion'];
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

<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta name="renderer" content="webkit">
        <meta name="format-detection" content="telephone=no"/>
        <title><?= isset($ctitle) ? $ctitle : Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head(); ?>
        <script type="text/javascript">
            $(function () {
                hmsr();
                addToken();
            });
        </script>
    </head>
    <body>
        <?php $this->beginBody(); ?>
            <?= $content ?>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>