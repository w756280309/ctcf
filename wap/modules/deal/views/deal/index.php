<?php
$this->title = '我要理财';
$this->showBottomNav = true;
$this->backUrl = false;

use wap\assets\WapAsset;
use yii\web\JqueryAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditlist.css?v=20161019', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/list_tag.css', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI .'js/TouchSlide.1.1.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI .'js/jquery.classyloader.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI .'js/list.js?v=20160427', ['depends' => JqueryAsset::class, 'position' => 1]);

$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
$pc_cat = Yii::$app->params['pc_cat'];
$action = Yii::$app->controller->action->getUniqueId();
?>

<?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
    <div class="row list-title">
        <div class="col-xs-6"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">理财列表</a></div>
        <div class="col-xs-6"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让列表</a></div>
    </div>
<?php } ?>

<?php if ($deals) { ?>
    <div id="item-list">
        <?= $this->renderFile('@wap/modules/deal/views/deal/_more.php',['deals' => $deals, 'header' => $header]) ?>
    </div>
    <!--加载跟多-->
    <div class="load" style="display:block;"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>