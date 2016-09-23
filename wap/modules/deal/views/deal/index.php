<?php
$this->title = '我要理财';
$this->showBottomNav = true;
$this->backUrl = false;

$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
$pc_cat = Yii::$app->params['pc_cat'];
$action = Yii::$app->controller->action->getUniqueId();

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/index.css?v=20160505"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/list_tag.css"/>
<script src="<?= ASSETS_BASE_URI ?>js/TouchSlide.1.1.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/jquery.classyloader.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/list.js?v=20160427"></script>

<div class="row list-title">
    <div class="col-xs-6"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">理财列表</a></div>
    <div class="col-xs-6"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让列表</a></div>
</div>

<?php if ($deals) { ?>
    <div id="item-list">
        <?= $this->renderFile('@wap/modules/deal/views/deal/_more.php',['deals' => $deals, 'header' => $header]) ?>
    </div>
    <!--加载跟多-->
    <div class="load" style="display:block;"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>