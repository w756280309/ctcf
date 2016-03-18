<?php
$this->title="合同说明";

$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css', ['depends' => 'frontend\assets\WapAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/swiper.min.css', ['depends' => 'frontend\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.js');
$this->registerJsFile(ASSETS_BASE_URI.'js/swiper.min.js');
$this->registerJsFile(ASSETS_BASE_URI.'js/licai.js');

$switcherJs = <<<'JS'
var $switcher = $('#legal-docs-switcher');
var activeIndex = $switcher.find('.dian').data('switcherIndex');
if (activeIndex > 2) {
var docSwiper = $switcher[0].swiper;
docSwiper.slideTo(activeIndex);
}
JS;
$this->registerJs($switcherJs);

$num = 0;
?>
<style>
    body {
        background-color: #fff;
    }
    .container-text {
        padding:4px 10px 64px;
        font-size: 12px;
        background: #fff;
        font-family:"微软雅黑", Arial
    }
    .list-txt{
        margin:1px 0 3px;
        font-size: 12px;
        color:#3e3a39;
        line-height: 18px;
    }
    h4.agree_title {
        margin:5px auto;
        text-align: center;
        font-size: 14px;
        color: #000;
        line-height: 18px;
    }
</style>

<!-- Swiper -->
<div id="legal-docs-switcher" class="swiper-container" style="line-height: 18px;">
    <div class="swiper-wrapper">
        <?php foreach($model as $key => $val): $num++; ?>
        <div data-switcher-index="<?= $num-1 ?>" class="swiper-slide <?= $key_f == $key?"dian":"" ?>" onclick="location.replace('/order/order/agreement?id=<?= $val['pid'] ?>&key=<?= $key ?>')"><?= Yii::$app->functions->cut_str($val['name'],5,0,'**') ?></div>
        <?php endforeach; ?>
    </div>
</div>
<div class="container-text" data-title="<?= $num ?>">
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <h4 class="agree_title"><?= $model[$key_f]['name'] ?></h4>
        </div>
        <div class="col-xs-1"></div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= html_entity_decode($content) ?></div>
        </div>
    </div>
</div>
