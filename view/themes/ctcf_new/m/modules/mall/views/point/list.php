<?php

$this->title = '积分明细';

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/jiaoyimingxi.css', ['depends' => WapAsset::class]);

?>
<script type="text/javascript">
    var url = '/mall/point/list';
    var tp = '<?= $header['tp'] ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js?v=20180905"></script>

<?php if ($points) { ?>
    <?= $this->render('_list', ['points' => $points]) ?>
    <div class="load"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>