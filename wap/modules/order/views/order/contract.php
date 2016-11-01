<?php
$this->title="合同说明";

$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css', ['depends' => 'wap\assets\WapAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/swiper.min.css', ['depends' => 'wap\assets\WapAsset']);
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
    
    $('.swiper-wrapper div').on('click',function() {
        var index=$('.swiper-wrapper div').index(this);
        
        $('.swiper-wrapper div').removeClass('dian');
        $('.swiper-wrapper div').eq(index).addClass('dian');
    })
    
    var $switcher = $('#legal-docs-switcher');
    var activeIndex = $switcher.find('.dian').data('switcherIndex');
    if (activeIndex > 2) {
        var docSwiper = $switcher[0].swiper;
        docSwiper.slideTo(activeIndex);
    }
});
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
    .bao_quan_button{
        text-align: right;
        position: fixed;
        top:80%;
        right: 20px;
    }
    .bao_quan_button button{
        display: block;
    }
</style>

<!-- Swiper -->
<div id="legal-docs-switcher" class="swiper-container" style="line-height: 18px;">
    <div class="swiper-wrapper">
        <?php foreach($contracts as $key => $contract) : $num++; ?>
            <div data-switcher-index="<?= $num - 1 ?>" class="swiper-slide <?= $fk == $key ? 'dian' : '' ?>" onclick="location.replace('/order/order/contract?asset_id=<?= $asset_id ?>&key=<?= $key ?>')">
                <?= $contract['title']?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="container-text" data-title="<?= $num ?>">
    <div class="row">
        <div class="col-xs-12">
            <div class="list-txt"><?= html_entity_decode($content) ?></div>
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