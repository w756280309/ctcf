<?php
$this->title = '联系我们';

$this->registerCssFile(ASSETS_BASE_URI . 'css/contact.css');
?>

<div class="container relation" >
    <!-- 主体 -->
    <!-- banner  -->
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>images/relation-ico.png" alt="温都金服地图" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">
        <div class="xinxi"><p class="p_float">公司地址&nbsp;:</p><p class="p_num">温州市鹿城区飞霞南路657号保丰大楼四层</p></div>
        <div class="xinxi"><p class="p_float">工作时间&nbsp;:</p><p class="p_num">8:30-17:30（周一至周五）</p></div>
        <div class="xinxi"><p class="p_float">客服电话&nbsp;:</p><p class="p_num"><?= Yii::$app->params['contact_tel'] ?></p></div>
        <div class="xinxi"><p class="p_float">客服时间&nbsp;:</p><p class="p_num">8:30-20:00（周一至周日，假日例外）</p></div>
        <div class="xinxi"><p class="p_float">客服QQ&nbsp;&nbsp;:</p><p class="p_num">1430843929</p></div>
        <div class="mendian"><p class="p_float">保丰门店:</p><p class="p_num">温州市鹿城区飞霞南路657号保丰大楼一层</p></div>
        <div class="xinxi"><p class="p_float">工作时间:</p><p class="p_num">8:30-17:30（周一至周日）</p></div>
    </div>
</div>
