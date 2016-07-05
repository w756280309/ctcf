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
<script src="<?= ASSETS_BASE_URI ?>js/licai.js?v=20160413"></script>

    <!-- Swiper -->
    <div class="row" id='licai-title'>
    	<div class="col-xs-4">
    		<div class="licai-title1"><img src="<?= ASSETS_BASE_URI ?>images/licaiGang.png" alt=""> 累计投资金额</div>
                <div class="licai-money"><?= rtrim(rtrim(number_format($list['totalFund'], 2), '0'), '.') ?><span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title2"><img src="<?= ASSETS_BASE_URI ?>images/licaiMoney.png" alt=""> 累计获得收益</div>
                <div class="licai-money"><?= rtrim(rtrim(number_format($profitFund, 2), '0'), '.') ?><span>元</span></div>
    	</div>
    	<div class="col-xs-4">
    		<div class="licai-title3"><img src="<?= ASSETS_BASE_URI ?>images/licaiHead.png" alt=""> 待还清项目</div>
                <div class="licai-money"><?= $list['daihuan'] ?><span>个</span></div>
    	</div>
    </div>
    <div class="row" id='licai-xiangmu'>
    	<div class="col-xs-12">我投资过的项目</div>
    </div>

<?php if($list['data'])  { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_order_list.php',['list' => $list, 'type' => $type, 'profitFund' =>Yii::$app->user->getIdentity()->lendAccount->profit_balance])?>
    <div class="load" style="display: block"></div>
 <?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>