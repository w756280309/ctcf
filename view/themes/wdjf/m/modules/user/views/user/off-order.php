<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-26
 * Time: 上午11:21
 */

use wap\assets\WapAsset;

$this->title = '门店理财';
$this->backUrl = $backUrl ? $backUrl : '/user/user';
$back = $backUrl ? urlencode(Yii::$app->request->hostInfo.$backUrl) : '';

$this->registerCssFile(ASSETS_BASE_URI.'css/bind.css?v=1', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/licai.css?v=20160927', ['depends' => WapAsset::class]);

?>
    <script type="text/javascript">
        var url = '/user/user/myofforder?type=<?= $type ?>';
        var tp = '<?= $pages->pageCount ?>';
    </script>
    <script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="row list-title">
    <div class="col-xs-4"><a href="/user/user/myofforder?type=1&back_url=<?= $back ?>" class="<?= 1 === $type ? 'active-trans-title' : '' ?> trans-title">收益中</a></div>
    <div class="col-xs-4"><a href="/user/user/myofforder?type=2&back_url=<?= $back ?>" class="<?= 2 === $type ? 'active-trans-title' : '' ?> trans-title">待成立</a></div>
    <div class="col-xs-4"><a href="/user/user/myofforder?type=3&back_url=<?= $back ?>" class="<?= 3 === $type ? 'active-trans-title' : '' ?> trans-title">已还清</a></div>
</div>
<?php if(!empty($model))  { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_offline_order_list.php', ['model' => $model, 'type' => $type]) ?>
    <div class="load"></div>
<?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>