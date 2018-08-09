<?php

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\widgets\Pager;
use common\view\LoanHelper;
use frontend\assets\FrontAsset;

$this->title = '我要理财';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/deallist.css?v=170718', ['depends' => FrontAsset::class]);

$action = Yii::$app->controller->action->getUniqueId();
$user = Yii::$app->user->getIdentity();
//是否展示转让
$showTransfer = !empty($user) && ($user->orderCount() > 0 || $user->creditOrderCount() > 0);
?>

<div class="projectContainer">
    <div class="projectList">
        <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
            <div class="alist-box">
                <?php if ($showTransfer) : ?>
                    <a href='/licai/notes' class="list-span <?= 'licai/notes' === $action ? 'select-span' : '' ?>">转让</a>
                <?php endif; ?>
                <a href='/licai/loan' class="list-span <?= 'licai/loan' === $action ? 'select-span' : '' ?>">网贷</a>
                <a href='/licai/' class="list-span <?= 'licai/index' === $action ? 'select-span' : '' ?>">定期</a>
            </div>
        <?php } ?>
        <!--预告期-->
        <?php foreach ($loans as $key => $val) : ?>
        <a target="_blank" href="/deal/deal/detail?sn=<?= $val->sn ?>">
            <div class="deal-single loan <?= $key === count($loans) - 1 ? 'last' : '' ?> <?= in_array($val->status, [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW]) ? 'deal-single-border' : '' ?>">
                <div class="clearfix">
                    <?php if ($val->is_xs && in_array($val->status, [1,2])) { ?>
                        <?php if ($val->end_date >= time()) :?>
                            <div class="newer"></div>
                        <?php endif;?>
                    <?php } ?>
                    <!--类btn_ing_border为预告期和可投期的红边框-->
                    <div class="single_left">
                        <div class="single_title">
                            <p class="p_left"><?= $val->title ?></p>
                            <p class="p_right" title=""><?= Yii::$app->params['refund_method'][$val->refund_method] ?></p>
                            <div class="clear"></div>
                        </div>
                        <?php if (!empty($val->tags) || $val->pointsMultiple > 1) : ?>
                            <div class="deal_tips">
                                <?= $this->renderFile("@common/views/tags.php", ['loan' => $val]) ?>
                            </div>
                        <?php endif; ?>
                        <div class="center-border"></div>
                        <div class="single_content">
                            <ul class="single_ul_left">
                                <li class="li_1 <?= $val->isFlexRate && $val->jiaxi ? 'rate-steps-jiaxi' : '' ?>">
                                    <i class="float-left"><?= LoanHelper::getDealRate($val) ?></i><span>%</span><?php if (!empty($val->jiaxi)) { ?><span class="addRadeNumber">+<?= StringUtils::amountFormat2($val->jiaxi) ?>%</span><?php } ?>
                                </li>
                                <li class="li_2"><?= $val->cid === 3 ? '约定利率' : '预期年化收益率' ?></li>
                            </ul>
                            <ul class="single_ul_center">
                                <li class="li_1"><?php $ex = $val->getDuration(); ?><?= $ex['value']?><span><?= $ex['unit'] ?></span></li>
                                <li class="li_2">项目期限</li>
                            </ul>

                            <ul class="single_ul_right">
                                <li class="li_1"><?= StringUtils::amountFormat2($val->start_money) ?><span>元</span></li>
                                <li class="li_2">起投金额</li>
                            </ul>
                            <ul class="single_ul_right-add">
                                <li class="li_1"><?= StringUtils::amountFormat1('{amount}<span>{unit}</span>', $val->money) ?></li>
                                <li class="li_2">项目总额</li>
                            </ul>
                        </div>
                    </div>
                    <?php if (!in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) { ?>
                        <div class="single_right">
                            <div class="single_right_tiao">
                                <div class="tiao_content">
                                    <span class="tiao_content_length" style="width:<?= $val->getProgressForDisplay()?>%"></span>
                                </div>
                                <span class="single_right_tiao_span"><?= $val->getProgressForDisplay()?>%</span>
                                <div class="clear"></div>
                                <p class="remain-number">可投余额：<?= ($val->status == 1) ? (StringUtils::amountFormat1('{amount}{unit}', $val->money)) : StringUtils::amountFormat2($val->getLoanBalance()).'元' ?></p>
                            </div>
                            <?php if($val->end_date < time()) { ?>
                                <span class="single_right_button_over">已成立</span>
                            <?php } else { ?>
                                <?php
                                if (OnlineProduct::STATUS_PRE === $val->status) {
                                    $dates = Yii::$app->functions->getDateDesc($val->start_date);
                                    ?>
                                    <span class="single_right_button"><?= $dates['desc'] . date('H:i', $dates['time']) ?>起售</span>
                                <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                                    <span class="single_right_button">立即投资</span>
                                <?php } elseif (OnlineProduct::STATUS_FOUND === $val->status) { ?>
                                    <span class="single_right_button_over">已成立</span>
                                <?php } else { ?>
                                    <span class="single_right_button_over"><?= \Yii::$app->params['deal_status'][$val->status] ?></span>
                                <?php } ?>
                            <?php }?>
                        </div>
                    <?php } else { ?>
                        <div class="single_right">
                            <div class="single_right_over"><?= \Yii::$app->params['deal_status'][$val->status] ?>...</div>
                            <img class="shouyi-img" alt="" src="<?= ASSETS_BASE_URI ?>images/shouyizhong_icon.png">
                        </div>
                    <?php } ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
        <center><?= Pager::widget(['pagination' => $pages]); ?></center>
    </div>
</div>
