<?php

$this->title = '兑换记录';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/exchange/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/zepto.min.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<div class="flex-content">
    <div class="topTitle f18">
        <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="history.go(-1)">
        兑换记录
    </div>
    <div class="exchange-record">
        <ul id="exchange-list">
            <li class="clearfix" data-index="1">
                <div class="lf"><p>20元面值代金券</p><p>2016-12-11  12:39:03</p></div>
                <span class="lf"><i>25</i>积分</span>
                <button class="rg eschanging">领取</button>
            </li>
            <li class="clearfix" data-index="2">
                <div class="lf"><p>20元面值代金券</p><p>2016-12-11  12:39:03</p></div>
                <span class="lf"><i>25</i>积分</span>
                <button class="rg eschanged">已领取</button>
            </li>
        </ul>
    </div>
    <div id="pullUp">
        <span class="pullUpLabel f15">加载更多...</span>
    </div>
</div>

<script type="text/javascript">
   $(function () {
       $('#exchange-list li .eschanging').on('click', function () {
           var $this = $(this);
           var id = $this.parent().attr('data-index');

           var xhr = $.get('/mall/point/prize', {id: id}, function (data) {
               if (data.code) {
                   toastCenter(data.message);
               } else {
                   toastCenter(data.message, function () {
                       $this.removeClass('eschanging').addClass('eschanged').html('已领取');
                   });
               }
           });

           xhr.fail(function () {
               toastCenter('系统繁忙, 请稍后重试!');
           })
       })
   })
</script>
