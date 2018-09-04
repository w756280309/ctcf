 <?php

use wap\assets\WapAsset;

$this->title = '我的理财';
$this->backUrl = $backUrl ? $backUrl : '/user/user';
$back = $backUrl ? urlencode(Yii::$app->request->hostInfo.$backUrl) : '';

$this->registerCssFile(ASSETS_BASE_URI.'css/bind.css?v=1', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css?v=20160927', ['depends' => WapAsset::class]);

?>
<script type="text/javascript">
    var url = '/user/user/myorder?type=<?= $type ?>';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js?v=20180905"></script>

<div class="row list-title">
    <div class="col-xs-4"><a href="/user/user/myorder?type=1&back_url=<?= $back ?>" class="<?= 1 === $type ? 'active-trans-title' : '' ?> trans-title">收益中</a></div>
    <div class="col-xs-4"><a href="/user/user/myorder?type=2&back_url=<?= $back ?>" class="<?= 2 === $type ? 'active-trans-title' : '' ?> trans-title">待成立</a></div>
    <div class="col-xs-4"><a href="/user/user/myorder?type=3&back_url=<?= $back ?>" class="<?= 3 === $type ? 'active-trans-title' : '' ?> trans-title">已还清</a></div>
</div>

<?php if(!empty($model))  { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_order_list.php', ['model' => $model, 'type' => $type]) ?>
    <div class="load"></div>
 <?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>