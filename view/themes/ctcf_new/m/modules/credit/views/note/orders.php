<?php
$this->title = '转让记录';

use yii\helpers\Html;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditcontent.css', ['depends' => 'wap\assets\WapAsset']);
?>
<script type="text/javascript">
    var url = '/credit/note/orders?id=<?= Html::encode($id) ?>';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js?v=20180905"></script>

<div class="container credit-content">
    <div class="row cre-list">
        <div class="col-xs-4 cre-title">受让人</div>
        <div class="col-xs-4 cre-title">出借时间</div>
        <div class="col-xs-4 cre-title">出借金额(元)</div>
    </div>
    <?php if (!empty($orders)) { ?>
        <?= $this->renderFile('@wap/modules/credit/views/note/_more_order.php', ['orders' => $orders, 'users' => $users]) ?>
        <div class="load"></div>
    <?php } else { ?>
        <div class="cre-list-nums-none">暂无数据</div>
    <?php } ?>
</div>