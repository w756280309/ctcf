<?php
$this->title = '安全保障';

$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/common/base.css');
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/common/footer171028.css');
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/safeguard/index.min.css');
$this->registerJsFile(ASSETS_BASE_URI . 'ctcf/js/libs/lib.flexible3.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);

?>

<!-- 主体 -->
<ul>
    <li>
        <a href="/safeguard/major-regard" class="clearfix">
            <div class="safe-title-img lf">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/safeguard/major_regard.png" alt="">
            </div>
            <div class="rg safe-title-part">
                <h5>专业合规</h5>
                <p>为什么在楚天财富投资是安全的？</p>
            </div>
        </a>
    </li>
    <li>
        <a href="/safeguard/risk-regard" class="clearfix">
            <div class="safe-title-img lf">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/safeguard/risk_regard.png" alt="">
            </div>
            <div class="rg safe-title-part">
                <h5>风控先进</h5>
                <p>楚天财富理财的风控先进在哪？</p>
            </div>
        </a>
    </li>
    <li>
        <a href="/safeguard/information-regard" class="clearfix">
            <div class="safe-title-img lf">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/safeguard/infamation_regard.png" alt="">
            </div>
            <div class="rg safe-title-part">
                <h5>信息安全</h5>
                <p>楚天财富怎么保证客户的信息安全？</p>
            </div>
        </a>
    </li>
    <li>
        <a href="/safeguard/data-regard" class="clearfix">
            <div class="safe-title-img lf">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/safeguard/data_regard.png" alt="">
            </div>
            <div class="rg safe-title-part">
                <h5>数据保全</h5>
                <p>楚天财富线上的电子合同是否有效？</p>
            </div>
        </a>
    </li>
</ul>

