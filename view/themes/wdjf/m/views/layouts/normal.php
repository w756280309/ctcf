<?php

use common\models\growth\PageMeta;
use common\view\AnalyticsHelper;
use yii\helpers\Html;

AnalyticsHelper::registerTo($this);
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
$this->registerJsFile(FE_BASE_URI . 'res/js/js.cookie.js', ['depends' => \yii\web\JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'res/js/lib.js?v=20170216', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('$(function () {
                hmsr();
            });', 3);
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
        <?php $this->head(); ?>
	    <style>
		    body {
			    -webkit-text-size-adjust: 100% !important;
		    }
	    </style>
    </head>
    <body>
        <?php $this->beginBody(); ?>
            <?= $content ?>
        <?php $this->endBody(); ?>
        <script>
          (function() {
            if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
              handleFontSize();
            } else {
              document.addEventListener("WeixinJSBridgeReady", handleFontSize, false);
            }
            function handleFontSize() {
              // 设置网页字体为默认大小
              WeixinJSBridge.invoke('setFontSizeCallback', { 'fontSize' : 0 });
              // 重写设置网页字体大小的事件
              WeixinJSBridge.on('menu:setfont', function() {
                WeixinJSBridge.invoke('setFontSizeCallback', { 'fontSize' : 0 });
              });
            }
          })();
        </script>
    </body>
</html>
<?php $this->endPage(); ?>