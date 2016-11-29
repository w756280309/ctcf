<?php

use common\view\AnalyticsHelper;
use common\view\WxshareHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.cookie.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'js/hmsr.js', ['depends' => JqueryAsset::class, 'position' => 1]);

AnalyticsHelper::registerTo($this);
WxshareHelper::registerTo($this, $this->share);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Yii::$app->params['wap_page_keywords'],
]);
$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::$app->params['wap_page_descritpion'],
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
        <title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head(); ?>
        <script>
            $(function() {
                $(document).ajaxSend(function(event, jqXHR, settings) {
                    var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
                    if (match) {
                        var val = decodeURIComponent(match[1].replace(/\+/g, " "));
                        settings.url = settings.url+(settings.url.indexOf('?') >= 0 ? '&' : '?')+'token='+encodeURIComponent(val);
                    }
                });
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