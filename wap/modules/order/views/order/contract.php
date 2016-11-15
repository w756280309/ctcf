<?php
$this->title="合同说明";

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/swiper.min.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/contract.css?v=20161114', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/swiper.min.js');

$count = count($contracts);
$switcherJs = <<<JS
$(function() {
    var num = $count;
    if(num >= 3) {
        num = 3;
    }
    var swiper = new Swiper('.swiper-container', {
        direction: "horizontal",
        pagination: '.swiper-pagination',
        paginationClickable: true,
        slidesPerView: num,
        spaceBetween: 50,
        breakpoints: {
            1024: {
                slidesPerView: num,
                spaceBetween: 40
            },
            768: {
                slidesPerView: num,
                spaceBetween: 30
            },
            640: {
                slidesPerView: num,
                spaceBetween: 20
            },
            320: {
                slidesPerView: num,
                spaceBetween: 10
            }
        }
    });
    
    $('.swiper-wrapper div').on('click', function() {
        var index = $('.swiper-wrapper div').index(this);
        var url = $(this).attr('url');
        
        $('.swiper-wrapper div').removeClass('dian');
        $('.swiper-wrapper div').eq(index).addClass('dian');
        
        $.get(url, function(data) {
            $('#content').html(data);
        })
    });
});
JS;
$this->registerJs($switcherJs);

$_js = <<<JS2
    $(function () {
        $('#content').on('click', '.download-ybq', function(e) {
            e.preventDefault();
            
            $('#mask-back').addClass('show');
            $('#mask').addClass('show');
        });
        
        $('#content').on('click', '.true', function(e) {
            e.preventDefault();
            
            $('#mask-back').removeClass('show');
            $('#mask').removeClass('show');
        });
    });
JS2;

if (isset($bq) && isset($isDisDownload) && $isDisDownload) {
    $this->registerJs($_js);
}


$num = 0;
$action = Yii::$app->controller->action->getUniqueId();
?>

<!-- Swiper -->
<div id="legal-docs-switcher" class="swiper-container" style="line-height: 18px;">
    <div class="swiper-wrapper">
        <?php foreach($contracts as $key => $contract) : ?>
            <?php $url = 'order/order/agreement' === $action ? "/order/order/agreement?id=$id&note_id=$note_id&key=$key" : "/order/order/contract?asset_id=$asset_id&key=$key"; ?>
            <div data-switcher-index="<?= $key ?>" class="swiper-slide <?= $fk == $key ? 'dian' : '' ?>" url="<?= $url ?>">
                <?= $contract['title'] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div id="content">
    <?= $this->renderFile('@wap/modules/order/views/order/_contract.php', array_merge(['content' => $content], 'order/order/agreement' === $action ? [] : ['bq' => $bq, 'isDisDownload' => $isDisDownload])) ?>
</div>
