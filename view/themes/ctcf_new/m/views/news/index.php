<?php
$this->title = '最新资讯';

$this->registerJsFile(ASSETS_BASE_URI . 'js/jquery-dateFormat.min.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/news.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
?>
<link href="<?= ASSETS_BASE_URI ?>css/news.css" rel="stylesheet">

<!-- 主体 -->
<div class="information-list">
    <?php foreach ($model as $val) : ?>
        <a href="/news/detail?id=<?= $val['id'] ?>" class="single">
            <p class="single-title">【资讯标题】<?= $val['title'] ?></p>
            <p class="single-description"><?= $val['summary'] ?></p>
            <p class="single-time"><?= empty($val['news_time']) ? '' : date('Y-m-d', $val['news_time']) ?></p>
        </a>
    <?php endforeach; ?>
    <!--加载跟多-->
    <div class="load"></div>
</div>

