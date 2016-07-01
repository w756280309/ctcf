<?php
$this->title = '理财列表';
$this->showBottomNav = true;
$this->backUrl = false;

$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
$pc_cat = Yii::$app->params['pc_cat'];
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/index.css?v=20160505"/>
<script src="<?= ASSETS_BASE_URI ?>js/TouchSlide.1.1.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/jquery.classyloader.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/list.js?v=20160427"></script>

<?php if ($deals) { ?>
    <div id="item-list">
        <?= $this->renderFile('@wap/modules/deal/views/deal/_more.php',['deals' => $deals, 'header' => $header]) ?>
    </div>
    <!--加载跟多-->
    <div class="load" style="display:block;"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>