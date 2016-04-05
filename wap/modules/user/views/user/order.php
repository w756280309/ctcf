<?php
$this->title="我的理财";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/licai.css"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/swiper.min.css">
<script type="text/javascript">
    var total='<?=$list['header']['count'] ?>';
    var size='<?=$list['header']['size'] ?>';
    var tp='<?=$list['header']['tp'] ?>';
    var cp='<?=$list['header']['cp'] ?>';
    var ctype='<?=(empty($type)?0:$type) ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/swiper.min.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/licai.js"></script>

    <!-- Swiper -->
    <div class="row" id='licai-title'>
    	<div class="col-xs-4">
    		<div class="licai-title1"><img src="<?= ASSETS_BASE_URI ?>images/licaiGang.png" alt=""> 累计投资金额</div>
                <div class="licai-money"><?= doubleval($list['totalFund']) ?><span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title2"><img src="<?= ASSETS_BASE_URI ?>images/licaiMoney.png" alt=""> 累计获得收益</div>
                <div class="licai-money"><?= doubleval($profitFund) ?><span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title3"><img src="<?= ASSETS_BASE_URI ?>images/licaiHead.png" alt=""> 待还清项目</div>
                <div class="licai-money"><?= $list['daihuan'] ?><span>个</span></div>
    	</div>
    </div>
    <div class="row" id='licai-xiangmu'>
    	<div class="col-xs-12">我投资过的项目</div>
    </div>

<?php if($list['data']) { foreach ($list['data'] as $o) { ?>
<div class="loan-box" onclick="location.href='/user/user/orderdetail?id=<?= $o['id'] ?>'">
    <div class="loan-title">
        <div class="title-overflow"><?=$o['title']?></div>
        <div class="loan-status <?= $o['classname'] ?>"><?=$o['statusval']?></div>
    </div>

    <div class="row loan-info">
        <div class="col-xs-8 loan-info1">
            <p><span class="info-label">认购金额：</span><span class="info-val"><?= $o['order_money'] ?>元</span></p>
            <?php if (0 === (int)$o['finish_date']) { ?>
                <p><span class="info-label">项目期限：</span><span class="info-val"><?= $o['expiress'].$o['method'] ?></span></p>
            <?php } else { ?>
                <p><span class="info-label">到期时间：</span><span class="info-val"><?= $o['returndate'] ?></span></p>
            <?php } ?>
        </div>
        <?php if ('--' !== $o['profit']) { ?>
        <div class="col-xs-4 loan-info2">
            <p class="info-val"><?= $o['profit'] ?>元</p>
            <p class="info-label"><?= ($o['pstatus']==6)?"实际收益":"预期收益" ?></p>
        </div>
        <?php } else { ?>
        <div class="col-xs-4 loan-info2">
            <p class="info-val"><?= $o['finish_rate'] ?>%</p>
            <p class="info-label">募集进度</p>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
    <div class="load" style="display: block"></div>
<?php } else{ ?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>
