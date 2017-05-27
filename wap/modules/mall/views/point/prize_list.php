<?php

$this->title = '兑换记录';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/exchange/css/index.css?v=20170519-v">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/zepto.min.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script type="text/javascript">
    var url = '/mall/point/prize-list';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="history.go(-1)">
            兑换记录
        </div>
    <?php } ?>

    <div class="exchange-record">
        <a href="/mall/portal/record" class="tips">暂显示4月21日起兑换的代金券，全部记录可前往积分商城查询</a>
        <?php if (!empty($vouchers)) { ?>
            <ul id="exchange-list">
                <?= $this->render('_prize_list', ['vouchers' => $vouchers]) ?>
                <div class="load"></div>
            </ul>
        <?php } else { ?>
            <div class="nodata" style="display: block;">暂无数据</div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
   $(function () {
       var allowClick = true;

       $('#exchange-list').on('click', '.eschanging', function () {
           if (!allowClick) {
               return;
           }

           var $this = $(this);
           var id = $this.parent().attr('data-index');
           allowClick = false;

           var xhr = $.get('/mall/point/prize', {id: id}, function (data) {
               if (data.code) {
                   toastCenter(data.message, function () {
                       allowClick = true;
                   });
               } else {
                   toastCenter(data.message, function () {
                       $this.removeClass('eschanging').addClass('eschanged').html('已领取');
                       $this.off('click');
                       allowClick = true;
                   });
               }
           });

           xhr.fail(function () {
               toastCenter('系统繁忙, 请稍后重试!', function () {
                   allowClick = true;
               });
           })
       })
   })
</script>
