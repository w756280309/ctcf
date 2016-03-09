<?php
$this->title="合同说明";
?>
 <link rel="stylesheet" href="/css/licai.css"/>
 <link rel="stylesheet" href="/css/swiper.min.css">
 <script src="/js/jquery.js"></script>
 <script src="/js/swiper.min.js"></script>
 <script src="/js/licai.js"></script>
 <style>
     .container-text {
         padding:4px 10px 64px;
         font-size: 12px;
         background: #fff;
         font-family:"微软雅黑", Arial
     }
     .list-txt{
         margin:1px 0 3px;
         font-size: 12px;
         color:#3e3a39;
         line-height: 18px;
     }
     h4.agree_title {
         margin:5px auto;
         text-align: center;
         font-size: 14px;
         color: #000;
         line-height: 18px;
     }
 </style>
    <!-- Swiper -->
    <div class="swiper-container" style="line-height: 18px;">
        <div class="swiper-wrapper" >
            <?php foreach($model as $key => $val): ?>
            <div class="swiper-slide <?= $key_f == $key?"dian":"" ?>" onclick="window.location.href='/order/order/agreement?id=<?= $val['pid'] ?>&key=<?= $key ?>'"><?= Yii::$app->functions->cut_str($val['name'],5,0,'**') ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container-text">
        <div class="row">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">
                <h4 class="agree_title"><?= $model[$key_f]['name'] ?></h4>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="list-txt"><?= html_entity_decode($content) ?></div>
            </div>
        </div>
    </div>

    <!--footer-->
    <div class="row navbar-fixed-bottom footer">
        <div class="col-xs-4 footer-title">
            <div class="footer-inner">
                <a href="/" class="shouye1"><span class="shouye"></span>首页</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner1">
                <a href="/deal/deal/index"><span class="licai"></span>理财</a>
            </div>
        </div>
        <div class="col-xs-4 footer-title">
            <div class="footer-inner2">
                <?php if (!\Yii::$app->user->isGuest) { ?>
                <a href="/user/user"><span class="zhanghu"></span>账户</a>
                <?php } else { ?>
                <a href="/site/login"><span class="zhanghu"></span>账户</a>
                <?php } ?>
            </div>
        </div>
    </div>

