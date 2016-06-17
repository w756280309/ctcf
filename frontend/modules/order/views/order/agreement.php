<?php
$this->title = '产品合同';

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/productcontract.css', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div class="contract-box clearfix">
    <div class="contract-container">
        <div class="contract-container-box">
            <p class="contract-title">产品合同</p>
            <div class="contract-content">
                <ul class="title-box">
                    <?php foreach ($model as $key => $val) : ?>
                        <li class="title <?= 0 === $key ? 'active' : '' ?>">
                            <a>
                                <?php
                                    if (0 === $key) {
                                        echo '认购合同';
                                    } elseif (1 === $key) {
                                        echo '风险提示书';
                                    } else {
                                        echo Yii::$app->functions->cut_str($val->name, 5, 0, '**');
                                    }
                                ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="pagination">
                    <?php foreach ($model as $key => $val) : ?>
                        <div class="list <?= 0 !== $key ? 'hide' : '' ?>">
                            <?= html_entity_decode($val->content) ?>

                            <?php if (isset($ebao[$key])) : ?>
                                <div class="a-btn">
                                    <?php if (isset($ebao[$key]['downUrl'])) : ?>
                                        <a class="a-lf" href="<?= $ebao[$key]['downUrl'] ?>">下载合同</a>
                                    <?php endif; ?>
                                    <?php if (isset($ebao[$key]['linkUrl'])) : ?>
                                        <a class="a-rg" href="<?= $ebao[$key]['linkUrl'] ?>">保全证书</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
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