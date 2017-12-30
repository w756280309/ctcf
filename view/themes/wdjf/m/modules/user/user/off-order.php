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
        var url = '/user/user/myofforder';
        var tp = '<?= $pages->pageCount ?>';
    </script>
    <script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<?php if(!empty($model))  { ?>
    <?= $this->renderFile('@wap/modules/user/views/user/_offline_order_list.php', ['model' => $model]) ?>
    <div class="load"></div>
<?php } else {?>
    <div class="nodata" style="display: block">暂无数据</div>
<?php } ?>