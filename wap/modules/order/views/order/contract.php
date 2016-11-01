<?php
$this->title="合同说明";

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/swiper.min.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/contract.css', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/swiper.min.js');

$switcherJs = <<<'JS'
$(function() {
    var num = $('.container-text').attr('data-title');
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
            $('.list-txt').html(data);
        })
    });
});
JS;
$this->registerJs($switcherJs);

$num = 0;
$action = Yii::$app->controller->action->getUniqueId();
?>

<!-- Swiper -->
<div id="legal-docs-switcher" class="swiper-container" style="line-height: 18px;">
    <div class="swiper-wrapper">
        <?php foreach($contracts as $key => $contract) : $num++; ?>
            <?php $url = 'order/order/agreement' === $action ? "/order/order/agreement?id=$id&note_id=$note_id&key=$key" : "/order/order/contract?asset_id=$asset_id&key=$key"; ?>
            <div data-switcher-index="<?= $num - 1 ?>" class="swiper-slide <?= $fk == $key ? 'dian' : '' ?>" url="<?= $url ?>">
                <?= $contract['title'] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="container-text" data-title="<?= $num ?>">
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= $content ?></div>
        </div>
    </div>
</div>
<?php if (isset($bq)) {?>
    <div class="bao_quan_button">
        <?php if (isset($bq['downUrl'])) : ?>
            <button class="btn btn-primary" onclick="window.location = '<?= $bq['downUrl'] ?>'" style="background:#f54337;border: 0px;">下载合同</button>
        <?php endif; ?>
        <?php if (isset($bq['linkUrl'])) : ?>
            <button class="btn btn-primary" onclick="window.location = '<?= $bq['linkUrl'] ?>'" style="background:#f54337;border: 0px;margin-top: 5px;">保全证书</button>
        <?php endif; ?>
    </div>
<?php } ?>