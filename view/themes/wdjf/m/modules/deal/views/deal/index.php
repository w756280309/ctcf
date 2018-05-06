<?php

use Lhjx\Http\HttpUtils;
use wap\assets\WapAsset;
use yii\web\JqueryAsset;

$this->title = '我要理财';
$this->showBottomNav = true;
$this->hideHeaderNav = HttpUtils::isWeixinRequest();
$this->backUrl = false;
$action = Yii::$app->controller->action->getUniqueId();

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditlist.css?v=20180505', ['depends' => WapAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/list_tag.css', ['depends' => WapAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI .'js/TouchSlide.1.1.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI .'js/jquery.classyloader.js', ['depends' => JqueryAsset::class, 'position' => 1]);
if ($action === 'deal/deal/index') {
    $this->registerJsFile(ASSETS_BASE_URI .'js/list.js?v=20160427', ['depends' => JqueryAsset::class, 'position' => 1]);
} elseif ($action === 'deal/deal/loan') {
    $this->registerJsFile(ASSETS_BASE_URI .'js/list-loan.js', ['depends' => JqueryAsset::class, 'position' => 1]);
}

$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
$pc_cat = Yii::$app->params['pc_cat'];

$user = Yii::$app->user->getIdentity();
?>
<?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
    <div class="row list-title">
<!--        区分大于5万显示南金中心，小于5万不显示-->
<!--        --><?php //if (!empty($user) && $user->isShowNjq) { ?>
<!--            <div class="col-xs-4"><a href="/deal/deal/index" class="cre-title --><?//= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?><!--">理财列表</a></div>-->
<!--            <div class="col-xs-4"><a href="/licai/notes" class="cre-title --><?//= $action === 'licai/notes' ? 'active-cre-title' : '' ?><!--">转让列表</a></div>-->
<!--            <div class="col-xs-4"><a href="/njq/loan-list" class="cre-title --><?//= $action === 'njq/loan/list' ? 'active-cre-title' : '' ?><!--">南金中心</a></div>-->
<!--        --><?php //} else { ?>
            <div class="col-xs-4"><a href="/deal/deal/loan" class="cre-title <?= $action === 'deal/deal/loan' ? 'active-cre-title' : '' ?>">定期</a></div>
            <div class="col-xs-4"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">网贷</a></div>
            <div class="col-xs-4"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让</a></div>
<!--        --><?php //} ?>
    </div>
<?php } ?>

<?php if ($action === 'deal/deal/loan' && !empty($user) && $user->getIsShowNjq(20000)) : ?>
    <div class="deal-njfae-hint">
    <?php if (!defined('IN_APP')) : ?>
        <a class="njfae-hint-banner" href="/njq/connect?redirect=<?= urlencode('site/index?utm_source='.$user->campaign_source) ?>">
    <?php endif; ?>
            <img src="<?= ASSETS_BASE_URI ?>images/product/notice_180506_750_285.png" alt="">
    <?php if (!defined('IN_APP')) : ?>
        </a>
    <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($deals) { ?>
    <div id="item-list">
        <?= $this->renderFile('@wap/modules/deal/views/deal/_more.php',['deals' => $deals, 'header' => $header]) ?>
    </div>
    <!--加载更多-->
    <div class="load"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>

