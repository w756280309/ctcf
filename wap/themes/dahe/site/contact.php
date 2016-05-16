<?php
$this->title = '联系我们';

$this->registerCssFile(ASSETS_BASE_URI . 'css/contact.css');
?>

<div class="container relation" >
    <!-- 主体 -->
    <div class="about-content row">
        <div class="xinxi"><p class="p_float">公司地址&nbsp;:</p><p class="p_num">河南省郑州市农业路东28号报业大厦1507</p></div>
        <div class="xinxi"><p class="p_float">工作时间&nbsp;:</p><p class="p_num">8:30-18:00（周一至周五）</p></div>
        <div class="xinxi"><p class="p_float">客服电话&nbsp;:</p><p class="p_num"><?= Yii::$app->params['contact_tel'] ?></p></div>
        <div class="mendian"><p class="p_num">郑州市城东路顺河路口南20米路东大河报广告中心营业部</p></div>
        <div class="xinxi"><p class="p_num">郑州市中原路与大学路交会处向北50米路东（大学路40号）大河广告中心营业部</p></div>
        <div class="xinxi"><p class="p_num">郑州市黄河路与政七街交叉口东50米路南 大河广告中心营业部</p></div>
    </div>
</div>
