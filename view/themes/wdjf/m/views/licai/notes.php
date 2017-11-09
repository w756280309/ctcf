<?php

use Lhjx\Http\HttpUtils;

$this->title = '我要理财';
$this->showBottomNav = true;
$this->hideHeaderNav = HttpUtils::isWeixinRequest();
$this->backUrl = false;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditlist.css?v=20161223', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/TouchSlide.1.1.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.classyloader.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/credit_page.js?v=171109', ['depends' => 'wap\assets\WapAsset']);

$this->registerJs('var tp = ' . $tp . ';', 1);
$this->registerJs("var url = '/licai/notes';", 1);

$action = Yii::$app->controller->action->getUniqueId();
?>

<div class="row list-title">
    <div class="col-xs-6"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">理财列表</a></div>
    <div class="col-xs-6"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让列表</a></div>
</div>

<?php if ($notes) { ?>
    <div id="credititem-list">
        <?= $this->renderFile('@wap/views/licai/_more_note.php', ['notes' => $notes, 'tp' => $tp]) ?>
    </div>
    <!--加载更多-->
    <div class="load"></div>
<?php } else { ?>
    <div class="cre-list-nums-none" style="display:block;">暂无数据</div>
<?php } ?>
