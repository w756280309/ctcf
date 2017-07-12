<?php
$this->title = '联系我们';

$this->registerCssFile(ASSETS_BASE_URI . 'css/contact.css');
?>

<div class="container relation" >
    <!-- 主体 -->
    <!-- banner  -->
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>images/ctcf/location.png" alt="楚天财富地图" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">
        <div class="xinxi"><p class="p_float">公司地址&nbsp;:</p><p class="p_num">武汉市东湖路181号楚天文化创意产业园8号楼1层</p></div>
        <div class="xinxi"><p class="p_float">客服电话&nbsp;:</p><p class="p_num"><a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p></div>
        <div class="xinxi"><p class="p_float">客服时间&nbsp;:</p><p class="p_num"><?= Yii::$app->params['platform_info.customer_service_time'] ?>（周一至周日，假日例外）</p></div>
        <div class="xinxi"><p class="p_float">客服QQ&nbsp;&nbsp;:</p><p class="p_num">3227992346</p></div>
    </div>
</div>
