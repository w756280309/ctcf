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
$user = Yii::$app->user->getIdentity();
?>

<!--<div class="row list-title">-->
<!--    <div class="col-xs-4"><a href="/deal/deal/index" class="cre-title --><?//= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?><!--">理财列表</a></div>-->
<!--    <div class="col-xs-4"><a href="/licai/notes" class="cre-title --><?//= $action === 'licai/notes' ? 'active-cre-title' : '' ?><!--">转让列表</a></div>-->
<!--    <div class="col-xs-4"><a href="/licai/notes" class="cre-title --><?//= $action === 'licai/notes' ? 'active-cre-title' : '' ?><!--">南金中心</a></div>-->
<!--</div>-->
<div class="row list-title">
    <!--        区分大于5万显示南金中心，小于5万不显示-->
<!--    --><?php //if (!empty($user) && $user->isShowNjq) { ?>
<!--        <div class="col-xs-4"><a href="/deal/deal/index" class="cre-title --><?//= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?><!--">理财列表</a></div>-->
<!--        <div class="col-xs-4"><a href="/licai/notes" class="cre-title --><?//= $action === 'licai/notes' ? 'active-cre-title' : '' ?><!--">转让列表</a></div>-->
<!--        <div class="col-xs-4"><a href="/njq/loan-list" class="cre-title --><?//= $action === 'njq/loan/list' ? 'active-cre-title' : '' ?><!--">南金中心</a></div>-->
<!--    --><?php //} else { ?>
        <div class="col-xs-6"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">理财列表</a></div>
        <div class="col-xs-6"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让列表</a></div>
<!--    --><?php //} ?>
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
