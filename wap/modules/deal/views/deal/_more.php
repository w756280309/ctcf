<?php foreach ($deals as $val):
    $is_hui = in_array($val->status, [4, 5, 6]); ?>
    <a class="row column dealdata" href="/deal/deal/detail?sn=<?= $val->sn ?>">
        <div class="col-xs-12 col-sm-10 column-title">
            <?php if (empty($val->jiaxi) || $val->isFlexRate) { ?>
                <img class="qian show" src="<?= ASSETS_BASE_URI ?>images/qian.png" alt="">
            <?php } else { ?>
                <img class="badges show" src="<?= ASSETS_BASE_URI ?>images/badge.png" alt="">
            <?php } ?>
            <span class="<?= $is_hui?'hui':'' ?>"><?= $val->title ?></span>
        </div>
        <div class="container" style="clear:both;">
            <ul class="row column-content">
                <li class="col-xs-4">
                    <div>
                    <span class="interest-rate <?= $is_hui?'hui':'' ?> rate-steps">
                        <?= $val->yield_rate ?><span class="column-lu">%</span>
                        <?php if (!empty($val->jiaxi) && !$val->isFlexRate) { ?><span class="bonus-badge <?= $is_hui?'hui':'' ?>">+<?= doubleval($val->jiaxi) ?>%</span><?php } ?>
                    </span>
                    </div>
                    <span class="desc-text nianRate <?= $is_hui?'hui':'' ?>">年化收益率</span>
                </li>
                <li class="col-xs-2 <?= $is_hui?'hui':'' ?>">
                    <p class="<?= $is_hui?'hui':'' ?>">
                        <?php $ex = $val->getDuration() ?><?= $ex['value'] ?><span class="column-lu"><?= $ex['unit']?></span>
                    </p>
                    <span class='desc-text <?= $is_hui?'hui':'' ?>'>期限</span>
                </li>
                <li class="col-xs-3 aa <?= $is_hui?'hui':'' ?>">
                    <p class="<?= $is_hui?'hui':'' ?>"><?= $val->start_money ?><span class="column-lu">元</span></p>
                    <span class='desc-text <?= $is_hui?'hui':'' ?>'>起投</span>
                </li>
                <li class="col-xs-3 bb nock1">
                    <div class="nock">
                        <canvas data-status="<?= $val->status ?>" data-per="<?= (7 === (int) $val->status) ? 100 : ($is_hui ? 0 : $val->finish_rate) ?>"></canvas>
                        <?php if ($val->status == 1) { ?>
                            <div class="column-clock"><span>
                                <?php $dates = Yii::$app->functions->getDateDesc($val->start_date); ?>
                                <?= $dates['desc'] ?>
                            </span><?= date('H:i', $val->start_date) ?></div>
                        <?php } else if ($val->status == 2) { ?>
                            <div class="column-clock column-clock_per"><?= $val->finish_rate ?>%</div>
                        <?php } else if ($val->status == 7) { ?>
                            <div class="column-clock column-clock_per">成立</div>
                        <?php } else { ?>
                            <div class="column-clock column-clock_per <?= $is_hui?'hui':'' ?>"><?=  Yii::$app->params['deal_status'][$val->status] ?></div>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </div>
        <div class="row list-line">
            <div class="col-xs-12">
                <div class="listLine-content">
                    <span><i>还款方式</i></span><span class="refund-desc">
                        <div class="listLine-point"></div><?= \Yii::$app->params['refund_method'][$val->refund_method] ?>
                    </span>
                </div>
            </div>
        </div>
    </a>
<?php endforeach; ?>