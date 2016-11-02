 <?php
$this->title="我的理财";
$this->backUrl = '/user/user';

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/bind.css', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css?v=20160927', ['depends' => WapAsset::class]);
?>
<script type="text/javascript">
    var url = '/user/user/myorder?type=<?= $type ?>';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="row list-title">
    <div class="col-xs-4"><a href="/user/user/myorder?type=1" class="<?= 1 === $type ? 'active-trans-title' : '' ?> trans-title">收益中</a></div>
    <div class="col-xs-4"><a href="/user/user/myorder?type=2" class="<?= 2 === $type ? 'active-trans-title' : '' ?> trans-title">待成立</a></div>
    <div class="col-xs-4"><a href="/user/user/myorder?type=3" class="<?= 3 === $type ? 'active-trans-title' : '' ?> trans-title">已还清</a></div>
</div>

<?php if(!empty($model))  { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_order_list.php', ['model' => $model, 'type' => $type]) ?>
    <div class="load"></div>
 <?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>