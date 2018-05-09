<?php

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\widgets\Pager;
use common\view\LoanHelper;
use frontend\assets\CtcfFrontAsset;

$this->title = '我要出借';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/financial.min.css?v=20180224111', ['depends' => CtcfFrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/pagination.css', ['depends' => CtcfFrontAsset::class]);

$action = Yii::$app->controller->action->getUniqueId();

?>

<div class="ctcf-container">
    <div class="main">
        <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
            <ul class="product-nav clear-fix fz18">
                <li class="lf <?= 'licai/index' === $action ? 'active-nav-product' : '' ?>"><a href='/licai/'>散标列表</a></li>
                <li class="lf <?= 'licai/notes' === $action ? 'active-nav-product' : '' ?>"><a href="/licai/notes">转让列表</a></li>
            </ul>
        <?php } ?>
        <div class="financial-product-list fz-gray">
            <ul class="financial-list">
                <!--预告期-->
                <?php foreach ($loans as $key => $val) : ?>
                <li class="financial-preview-period">
                    <a class="fz-gray" target="_blank" href="/deal/deal/detail?sn=<?= $val->sn ?>">
                        <div class="product-top-part">
                            <!--新手专享角标-->
                            <?php if ($val->is_xs) { ?>
                                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/sup_icon.png" alt="新手专享">
                            <?php } ?>
                            <div class="product-title clear-fix">
                                <h5 class="fz18 product-top-title overflow"><?= $val->title ?></h5>
                                <?php if (!empty($val->tags) || $val->pointsMultiple > 1) : ?>
                                        <?= $this->renderFile("@common/views/tags.php", ['loan' => $val]) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-bottom-part clear-fix">
                            <div class="interest-rate">
                                <div class="rate-top-part fz28 fz-orange-strong">
                                    <span class="fz42 "><?= LoanHelper::getDealRate($val) ?><?php if (!empty($val->jiaxi)) { ?><i class="add-rate-percentage fz16">+<?= StringUtils::amountFormat2($val->jiaxi) ?>%</i><?php } ?></span>%
                                </div>
                                <div class="rate-bottom-part fz14">借贷双方约定利率</div>
                            </div>
                            <div class="term-rate">
                                <div class="term-top-part fz18 fz-black">
                                    <span class="fz30"><?php $ex = $val->getDuration(); ?><?= $ex['value']?></span><?= $ex['unit'] ?>
                                </div>
                                <div class="term-bottom-part fz14">借款期限</div>
                            </div>
                            <div class="mode-style fz14">
                                <div class="mode-top-part">起借金额<span class="fz14 fz-black"><?= StringUtils::amountFormat2($val->start_money) ?>元</span></div>
                                <div class="mode-bottom-part clear-fix"><u class="lf">还款方式</u><span class="lf fz14 fz-black"><?= Yii::$app->params['refund_method'][$val->refund_method] ?></span></div>
                            </div>
                            <div class="raise-mode">
                                <div class="raise-top-part">借款金额<span class="fz14 fz-black"><?= StringUtils::amountFormat1('{amount}{unit}', $val->money) ?></span></div>
                                <div class="raise-bottom-part">剩余金额<span class="fz14 fz-black"><?= ($val->status == 1) ? (StringUtils::amountFormat1('{amount}{unit}', $val->money)) : StringUtils::amountFormat2($val->getLoanBalance()).'元' ?></span>
                                    <div class="speed-raise">
                                        <!--进度条-->
                                        <i style="width: <?= $val->getProgressForDisplay() ?>%;"></i>
                                    </div>
                                    <div class="speed-raise-number fz14"><?= $val->getProgressForDisplay()?>%</div>
                                </div>
                            </div>
                            <div class="btn-check">
                                <?php if (OnlineProduct::STATUS_PRE === $val->status) { ?>
                                    <div class="btn-light-orange">预告期</div>
                                <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                                    <div class="btn-orange">立即出借</div>
                                <?php } elseif (OnlineProduct::STATUS_FULL === $val->status || OnlineProduct::STATUS_FOUND === $val->status) { ?>
                                    <div class="btn-gray">已售罄</div>
                                <?php } elseif (OnlineProduct::STATUS_HUAN === $val->status) { ?>
                                    <div class="btn-light-orange">收益中</div>
                                <?php } elseif (OnlineProduct::STATUS_OVER === $val->status) { ?>
                                    <div class="btn-gray">已还清</div>
                                <?php } ?>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <center><?= Pager::widget(['pagination' => $pages]); ?></center>
        </div>
    </div>
</div>
