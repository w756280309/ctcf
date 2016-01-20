<?php
$this->title="合同协议";
?>
 <link rel="stylesheet" href="/css/bind.css"/>
 <link rel="stylesheet" href="/css/licai.css"/>
 <link rel="stylesheet" href="/css/swiper.min.css">
 <script src="/js/jquery.js"></script>
 <script src="/js/swiper.min.js"></script>
 <script src="/js/licai.js"></script>
  <style>
     .column {
        position: relative;
        height: auto;
        background: #fff;
        margin-top: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        overflow: hidden;
    }
    .agrname {
        margin-top: 24px;
        margin-left: 135px;
        margin-bottom: 18px;
        margin-right: 135px;
    }
    .content {
        margin-left: 30px;
        margin-right: 30px;
        color: #3d3a3b;
        font-size: 26px;
    }
 </style>
    
    <!-- Swiper -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach($model as $key => $val): ?>
            <div class="swiper-slide <?= $key_f == $key?"dian":"" ?>" onclick="window.location.href='/order/order/agreement?id=<?= $val['pid'] ?>&key=<?= $key ?>'"><?= Yii::$app->functions->cut_str($val['name'],5,0,'**') ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="column">
        <div class="agrname"><center><p style="font-size: 26px;color: #000000;"><?= $model[$key]['name'] ?></center></div>
        <div class="content"><?= html_entity_decode($content) ?></div>
    </div>

