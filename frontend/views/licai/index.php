<?php
$this->title = '我要理财';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/deallist.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\widgets\Pager;
?>

<div class="projectContainer">
    <div class="projectList">
        <!--预告期-->
        <?php foreach ($loans as $val) : ?>
            <a class="deal-single <?= in_array($val->status, [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW]) ? 'deal-single-border' : '' ?>" href="/deal/deal/detail?sn=<?= $val->sn ?>">
                <!--类btn_ing_border为预告期和可投期的红边框-->
                <div class="single_left">
                    <div class="single_title">
                        <p class="p_left"><?= $val->title ?></p>
                        <p class="p_right" title=""><?= Yii::$app->params['refund_method'][$val->refund_method] ?></p>
                        <div class="clear"></div>
                    </div>
                    <div class="single_content">
                        <ul class="single_ul_left">
                            <li class="li_1">
                                <?= rtrim(rtrim(number_format(OnlineProduct::calcBaseRate($val->yield_rate, $val->jiaxi), 2), '0'), '.') ?>
                                <?php if ($val->isFlexRate && $val->rateSteps) { ?>
                                    ~<?= rtrim(rtrim(number_format(RateSteps::getTopRate(RateSteps::parse($val->rateSteps)), 2), '0'), '.') ?><span>%</span>
                                <?php } elseif ($val->jiaxi) { ?>
                                    <span>%</span><span class="addRadeNumber">+<?= rtrim(rtrim(number_format($val->jiaxi, 2), '0'), '.') ?>%</span>
                                <?php } else { ?>
                                    <span>%</span>
                                <?php } ?>
                            </li>
                            <li class="li_2">年化收益率</li>
                        </ul>
                        <ul class="single_ul_center">
                            <li class="li_1"><?= $val->expires ?><span><?= $val->refund_method ? "天" : "个月" ?></span></li>
                            <li class="li_2">项目期限</li>
                        </ul>

                        <ul class="single_ul_right">
                            <li class="li_1"><?= rtrim(rtrim(number_format($val->start_money, 2), '0'), '.') ?><span>元</span></li>
                            <li class="li_2">起投金额</li>
                        </ul>
                        <ul class="single_ul_right-add">
                            <li class="li_1"><?= rtrim(rtrim(number_format($val->money, 2), '0'), '.') ?><span>元</span></li>
                            <li class="li_2">融资金额</li>
                        </ul>
                    </div>
                </div>
                <?php if (!in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) { ?>
                    <div class="single_right">
                        <div class="single_right_tiao">
                            <div class="tiao_content">
                                <span class="tiao_content_length" style="width:<?= number_format($val->finish_rate * 100) ?>px"></span>
                            </div>
                            <span class="single_right_tiao_span"><?= number_format($val->finish_rate * 100) ?>%</span>
                            <div class="clear"></div>
                            <p class="remain-number">可投余额：<?= number_format($val->money - $val->funded_money, 2) ?>元</p>
                        </div>
                        <?php
                            if (OnlineProduct::STATUS_PRE === $val->status) {
                                $dates = Yii::$app->functions->getDateDesc($val->start_date);
                        ?>
                            <span class="single_right_button"><?= $dates['desc'].date('H:i', $dates['time']) ?>起售</span>
                        <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                            <span class="single_right_button">立即投资</span>
                        <?php } else { ?>
                            <span class="single_right_button_over"><?= \Yii::$app->params['deal_status'][$val->status] ?></span>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="single_right">
                        <div class="single_right_over"><?= \Yii::$app->params['deal_status'][$val->status] ?>...</div>
                        <img class="shouyi-img" alt="" src="<?= ASSETS_BASE_URI ?>images/shouyizhong_icon.png">
                    </div>
                <?php } ?>
            </a>
        <?php endforeach; ?>
        <center><?= Pager::widget(['pagination' => $pages]); ?></center>
    </div>
</div>