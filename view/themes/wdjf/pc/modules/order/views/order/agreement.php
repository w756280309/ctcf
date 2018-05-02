<?php
$this->title = '产品合同';

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/productcontract.css', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div class="contract-box clearfix">
    <div class="contract-container">
        <div class="contract-container-box">
            <p class="contract-title">产品合同</p>
            <div class="contract-content">
                <?php if (is_array($contracts) && count($contracts) > 1) :?>
                    <ul class="title-box">
                        <?php foreach ($contracts as $key => $contract) : ?>
                            <li class="title <?= 0 === $key ? 'active' : '' ?>">
                                <a>
                                     <?= $contract['title']?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif;?>
                <div class="pagination">
                    <?php foreach ($contracts as $key => $contract) : ?>
                        <div class="list <?= 0 !== $key ? 'hide' : '' ?>">
                            <?= html_entity_decode($contract['content']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.title-box li').each(function() {
            var index=$(this).index();
            $('.title-box li').eq(index).on('click',function() {
                $('.title-box li').each(function() {$(this).removeClass('active')});
                $(this).addClass('active');
                $('.pagination .list').each(function() {$(this).addClass('hide')}).eq(index).removeClass('hide');
            });
        });
    });
</script>