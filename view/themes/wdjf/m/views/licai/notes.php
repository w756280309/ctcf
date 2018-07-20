<?php

use Lhjx\Http\HttpUtils;

$this->title = '我要理财';
$this->showBottomNav = true;
$this->hideHeaderNav = HttpUtils::isWeixinRequest();
$this->backUrl = false;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditlist.css?v=201612232', ['depends' => 'wap\assets\WapAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/credit/creditlist-search.css?v=201807196668', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/TouchSlide.1.1.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/jquery.classyloader.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/credit_page_wd.js?v=180719668812', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => 'wap\assets\WapAsset']);

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
            <div class="col-xs-4"><a href="/deal/deal/loan" class="cre-title <?= $action === 'deal/deal/loan' ? 'active-cre-title' : '' ?>">定期</a></div>
            <div class="col-xs-4"><a href="/deal/deal/index" class="cre-title <?= $action === 'deal/deal/index' ? 'active-cre-title' : '' ?>">网贷</a></div>
            <div class="col-xs-4"><a href="/licai/notes" class="cre-title <?= $action === 'licai/notes' ? 'active-cre-title' : '' ?>">转让</a></div>
<!--    --><?php //} ?>
</div>
<div class="search">
    <style>
        div.search .select-lists .select-box>div u{
            background: url(/images/licaiSelect/select_bottom_icon.png);
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
        }
        div.search .select-lists .select-box>div.select-div u {
            background: url(/images/licaiSelect/select_top_icon.png);
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
        }
    </style>
    <div class="search-box">
        <a style="background: url(/images/licaiSelect/search_toback_left.png) center center no-repeat;-webkit-background-size: contain;background-size: contain;" href="/licai/notes" class="go-back-search"></a>
        <div class="search-input-box">
            <img src="/images/licaiSelect/licai_search_icon.png" alt="">
            <input type="text" class="search-input" placeholder="输入标的名称">
        </div>
        <div class="search-box-btn">搜索</div>
    </div>
    <div style="display: none;" class="select-lists clearfix">
        <div class="select-box lf">
            <div id="select0">
                <span>项目期限</span>
                <u></u>
            </div>
            <div id="select1">
                <span>项目利率</span>
                <u></u>
            </div>
            <div id="select2">
                <span>折让率</span>
                <u></u>
            </div>
        </div>
        <div class="rg">
            <span class="now-to-search">搜索</span>
        </div>
    </div>
    <div style="" class="list-content">
        <div>
            <ul class="select0 select-ul">
                <?php if (!empty($selectSet['expireDaySets'])) :?>
                    <?php foreach ($selectSet['expireDaySets'] as $expireSet) :?>
                        <li k1="<?=$expireSet['expires'];?>" k2="day"><?= $expireSet['expires'];?>天</li>
                    <?php endforeach;?>
                <?php endif;?>
                <?php if (!empty($selectSet['expireMonthSets'])) :?>
                    <?php foreach ($selectSet['expireMonthSets'] as $expireMonthSet) :?>
                        <li k1="<?=$expireMonthSet['expires'];?>" k2="month"><?= $expireMonthSet['expires'];?>月</li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
            <ul class="select1 select-ul">
                <?php if (!empty($selectSet['projectRateSet'])) :?>
                    <?php foreach ($selectSet['projectRateSet'] as $projectRateSet) :?>
                        <li k4="<?=$projectRateSet['yield_rate'];?>"><?=bcmul($projectRateSet['yield_rate'], 100, 2)?>%</li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
            <ul class="select2 select-ul">
                <?php if (!empty($selectSet['discountRateSet'])) :?>
                    <?php foreach ($selectSet['discountRateSet'] as $discountRate) :?>
                        <li k5="<?=$discountRate['discountRate'];?>"><?=$discountRate['discountRate'];?>%</li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        </div>
    </div>
</div>
<div style="position: relative" class="all-list-item">
    <?php if ($notes) { ?>
        <div id="credititem-list">
            <div class="mark-top" style="position:relative;bottom: -10px;background: #fff;height: 41px;line-height: 41px;color:#131313;font-size:14px;padding-left: 4.5%;border-bottom: 1px solid #ddd;">推荐转让<img style="width:7.5%;margin-left: 1%;" src="/images/licaiSelect/tuijian.png" alt=""></div>
            <?= $this->renderFile('@wap/views/licai/_more_note.php', ['notes' => $notes, 'tp' => $tp]) ?>
        </div>
        <!--加载更多-->
        <div class="load"></div>
    <?php } else { ?>
        <div class="cre-list-nums-none" style="display:block;">暂无数据</div>
    <?php } ?>
</div>

