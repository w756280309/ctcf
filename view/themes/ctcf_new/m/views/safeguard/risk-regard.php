<?php
use wap\assets\WapAsset;

$this->title = '风控先进';
$this->registerJsFile(ASSETS_BASE_URI . 'ctcf/js/libs/lib.flexible3.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/safeguard/major.min.css');
?>
<link href="<?= ASSETS_BASE_URI ?>ctcf/css/common/base.css" rel="stylesheet">
<!-- 主体 -->
<div class="padding-contain">
    <div class="major-title-msg">
        <h5>楚天财富理财的风控先进在哪？</h5>
    </div>
    <h6 class="major-title-retract">（1）对接专业机构产品</h6>
    <p>楚天财富平台自身不生产资产。获取资产的方式是通过股东资源、合作机构、团队专业性等多重维度，筛选第三方优质资产方以拓展资产端资源，将其做好风控的项目或标的放到平台上与投资者对接。这些资产方包括银行类理财产品、优质企业政信类产品等优质债权提供方。</p>
</div>
