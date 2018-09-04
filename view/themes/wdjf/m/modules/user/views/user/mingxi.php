<?php
$this->title = '交易明细';

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/jiaoyimingxi.css?v=1', ['depends' => WapAsset::class]);
?>
<script type="text/javascript">
    var url = '/user/user/mingxi';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js?v=20180905"></script>

<?php if ($model) { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_mingxi_list.php', ['model' => $model]) ?>
    <div class="load"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>
