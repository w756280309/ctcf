<?php

use common\utils\StringUtils;
use yii\helpers\Html;

$this->title = '我的收益';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/ucenter/css/myIncome.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/zepto.min.js"></script>
<script type="text/javascript">
    var url = '/user/user/profit';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback lf" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="location.href='/user/user'">
            <?= Html::encode($this->title) ?>
        </div>
    <?php } ?>

    <ul class="titlebar clearfix">
        <li class="f15 lf"><a href="/user/user/assets">我的资产</a></li>
        <li class="f15 lf"><a class="actived" href="javascript:void(0);">我的收益</a></li>
    </ul>

    <ul class="income">
        <li class="f12 line-H32">累计收益(元)</li>
        <li class="f30 allIncome comRed"><?= StringUtils::amountFormat3($user->getProfit()) ?></li>
        <li class="f12 line-H32">累计资产(元)</li>
        <li class="f15 comRed"><?= StringUtils::amountFormat3($user->getTotalInvestment()) ?></li>
    </ul>

    <!--列表部分-->
    <div class="f12 listTitle"><span class="lf"></span> 最近收益</div>
    <?php if ($profits) { ?>
        <ul class="listView">
            <?= $this->renderFile('@wap/modules/user/views/user/_profit.php', ['profits' => $profits]) ?>
            <div class="load"></div>
        </ul>
    <?php } ?>

    <?php if(!$profits) { ?>
        <div align="center" style="line-height: 1.5rem;">暂无数据</div>
    <?php } ?>
</div>
