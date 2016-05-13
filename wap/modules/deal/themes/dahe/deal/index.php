<?php
frontend\assets\WapAsset::register($this);

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
    <?php foreach ($deals as $val):
        $is_hui = in_array($val['status'], [4, 5, 6]); ?>
    <a class="row column dealdata" href="/deal/deal/detail?sn=<?= $val['num'] ?>">
        <div class="col-xs-12 col-sm-10 column-title">
            <?php if (empty($val['jiaxi']) || $val['isFlexRate']) { ?>
            <img class="qian show" src="<?= ASSETS_BASE_URI ?>images/qian.png" alt="">
            <?php } else { ?>
            <img class="badges show" src="<?= ASSETS_BASE_URI ?>images/badge.png" alt="">
            <?php } ?>
            <span class="<?= $is_hui?'hui':'' ?>"><?= $val['title'] ?></span>
        </div>
        <div class="container" style="clear:both;">
            <ul class="row column-content">
                <li class="col-xs-4">
                    <div>
                    <span class="interest-rate <?= $is_hui?'hui':'' ?> rate-steps">
                        <?= $val['yr'] ?><span class="column-lu">%</span>
                        <?php if (!empty($val['jiaxi']) && !$val['isFlexRate']) { ?><span class="bonus-badge <?= $is_hui?'hui':'' ?>">+<?= doubleval($val['jiaxi']) ?>%</span><?php } ?>
                    </span>
                    </div>
                    <span class="desc-text nianRate <?= $is_hui?'hui':'' ?>">年化收益率</span>
                </li>
                <li class="col-xs-2 <?= $is_hui?'hui':'' ?>">
                    <p class="<?= $is_hui?'hui':'' ?>"><?= $val['qixian'] ?><span class="column-lu"><?= $val['method'] ?></span></p>
                    <span class='desc-text <?= $is_hui?'hui':'' ?>'>期限</span>
                </li>
                <li class="col-xs-3 aa <?= $is_hui?'hui':'' ?>">
                    <p class="<?= $is_hui?'hui':'' ?>"><?= $val['start_money'] ?><span class="column-lu">元</span></p>
                    <span class='desc-text <?= $is_hui?'hui':'' ?>'>起投</span>
                </li>
                <li class="col-xs-3 bb nock1">
                    <div class="nock">
                        <canvas data-status="<?= $val['status'] ?>" data-per="<?= (7 === (int) $val['status']) ? 100 : ($is_hui ? 0 : $val['finish_rate']) ?>"></canvas>
                    <?php if ($val['status'] == 1) { ?>
                        <div class="column-clock"><span><?= $val['start_desc'] ?></span><?= $val['start'] ?></div>
                    <?php } else if ($val['status'] == 2) { ?>
                        <div class="column-clock column-clock_per"><?= $val['finish_rate'] ?>%</div>
                    <?php } else if ($val['status'] == 7) { ?>
                        <div class="column-clock column-clock_per">成立</div>
                    <?php } else { ?>
                        <div class="column-clock column-clock_per <?= $is_hui?'hui':'' ?>"><?= $val['statusval'] ?></div>
                     <?php } ?>
                    </div>
                </li>
            </ul>
        </div>
        <div class="row list-line">
            <div class="col-xs-12">
                <div class="listLine-content">
                    <span><i>还款方式</i></span><span class="refund-desc">
                        <div class="listLine-point"></div><?= $val['cid'] ?>
                    </span>
                </div>
            </div>
        </div>
    </a>
<?php endforeach; ?>
    </div>
    <!--加载跟多-->
    <div class="load" style="display:block;"></div>
<?php } else { ?>
    <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>