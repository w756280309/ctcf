<?php
$this->title = '联系我们';

$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/linkman/contact.css?v=1.2');
?>

<div class="container relation" >
    <!-- 主体 -->
    <!-- banner  -->
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>ctcf/images/linkman/ico.png" alt="楚天金服地图" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">
        <div class="xinxi"><p class="p_float">公司地址&nbsp;:</p><p class="p_num">武汉市武昌区东湖路181号楚天文化创意产业园区8号楼1层</p></div>
        <div class="xinxi"><p class="p_float">客服电话&nbsp;:</p><p class="p_num"><a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p></div>
        <div class="xinxi"><p class="p_float">客服时间&nbsp;:</p><p class="p_num">9:00-20:00 （周一至周日，节假日例外）</p></div>
        <div class="xinxi"><p class="p_float">楚天财富客户交流群:</p><p class="p_num">474574526</p></div>
    </div>
</div>
