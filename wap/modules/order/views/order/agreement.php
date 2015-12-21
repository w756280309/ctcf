<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="合同协议";
?>
 <link rel="stylesheet" href="/css/bind.css"/>
 <link rel="stylesheet" href="/css/licai.css"/>
 <link rel="stylesheet" href="/css/swiper.min.css">
 <script src="/js/jquery.js"></script>
 <script src="/js/swiper.min.js"></script>
 <script src="/js/licai.js"></script>
    
    <!-- Swiper -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach($model as $key => $val): ?>
            <div class="swiper-slide <?= $key_f == $key?"dian":"" ?>" onclick="window.location.href='/order/order/agreement?id=<?= $val['pid'] ?>&key=<?= $key ?>'"><?= Yii::$app->functions->cut_str($val['name'],5,0,'**') ?></div>
            <?php endforeach; ?>
        </div>
    </div>

 <div><center><?= $content ?></center></div>
 