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
    .content {
        margin: 30px;
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
        <div><?= $agreement_name ?></div>
        <div class="content"><?= html_entity_decode($content) ?></div>
    </div>

