<?php

$this->title = '签到记录';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/qiandao/css/index_2.css?v=20170401">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script type="text/javascript">
    var url = '/user/checkin/list';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="history.go(-1)">
            签到记录
        </div>
    <?php } ?>

    <?php if ($pointOrders) { ?>
        <ul class="jilu-qd f15">
            <?= $this->render('_list', ['pointOrders' => $pointOrders]) ?>
            <div class="load"></div>
        </ul>
    <?php } else { ?>
        <p class="f20 no_jilu">暂无签到记录</p>
    <?php } ?>
</div>