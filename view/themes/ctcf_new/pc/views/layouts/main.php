<?php

use common\models\growth\PageMeta;
use common\view\AnalyticsHelper;
use frontend\assets\CtcfFrontAsset;
use yii\helpers\Html;

CtcfFrontAsset::register($this);
AnalyticsHelper::registerTo($this);

$meta = PageMeta::getMeta(Yii::$app->request);

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
        <title><?= isset($ctitle) ? $ctitle : Html::encode($this->title).' - 楚天财富' ?></title>
        <meta name="keywords" content="武汉理财|出借理财|个人理财|武汉楚天|武汉网贷|个人产品|国有理财平台|国有p2p平台|">
        <meta name="description" content="楚天财富（武汉）金融服务有限公司（www.hbctcf.com）是湖北日报新媒体集团控股子公司、具有国资背景的、专业从事互联网金融服务的企业，是湖北省首家按照省人民政府办公厅《关于规范发展民间融资机构的意见》（鄂政办发〔2014〕65号）文件精神设立的互联网金融服务公司，经过相关监管部门备案，明确以“个人、企业网络借贷信息中介服务”为主营业务。">
        <?php $this->head() ?>
        <link rel="shortcut icon" href="/ctcf/favicon.ico" type="image/x-icon" />
        <script type="text/javascript">
            $(function () {
                hmsr();
            })
        </script>
        <script>
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?779cc2b103544ff6e5f50e5f7c8bac2a";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
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
