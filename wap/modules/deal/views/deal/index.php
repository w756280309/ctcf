<?php
frontend\assets\WapAsset::register($this);
$curent_cid = $header['cat'];
$curent_xs = $header['xs'];
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
$this->registerJs('var cid=' . $curent_cid . ';', 1);
$this->registerJs('var xs=' . (empty($curent_xs)?'undefined':$curent_xs) . ';', 1);
$pc_cat = Yii::$app->params['pc_cat'];

?>
<script src="/js/jquery.classyloader.js"></script>
<div class="container">
    <div class="row tit-box">
        <?php foreach ($pc_cat as $key=>$cat){ ?>
            <div class="col-xs-4"><a <?php if ($key === (int)$curent_cid && null === $curent_xs){ ?> class="active" <?php } ?> href="/deal/deal/index?cat=<?= $key ?>"><?= $cat ?></a></div>
        <?php } ?>
        <div class="col-xs-4"><a <?php if ((int)$curent_xs === 1){ ?> class="active" <?php } ?> href="/deal/deal/index?xs=1">新手标</a></div>
    </div>
</div>
<div id="item-list">
    <?php foreach ($deals as $val): ?>
        <a class="row column dealdata" href="/deal/deal/detail?sn=<?= $val['num'] ?>">
            <!-- <div class="hidden-xs col-sm-1"></div> -->
            <div class="col-xs-12 col-sm-10 column-title"><img class="qian" src="/images/qian.png" alt=""><span><?= $val['title'] ?></span><img class="badges show" src="/images/badge.png" alt=""></div>
            <!-- <div class="<?= in_array($val['status'], [1, 2, 7]) ? "column-title-rg" : "column-title-rg1"; ?>"><?= $val['statusval'] ?></div> -->
            <div class="container">
                <ul class="row column-content">
                    <li class="col-xs-3">
                        <div>
                        <span class="interest-rate">
                            <?= doubleval($val['yr']) ?><span class="column-lu">%</span>
                            <?php if (!empty($val['jiaxi'])) { ?><span class="bonus-badge">+<?= doubleval($val['jiaxi']) ?>%</span><?php } ?>
                        </span>
                        </div>
                        <span class="desc-text">年化率</span>
                    </li>
                    <li class="col-xs-3">
                        <p><?= $val['qixian'] ?><span class="column-lu">天</span></p>
                        <span>期限</span>
                    </li>
                    <li class="col-xs-3">
                        <p class="">1000<span class="column-lu">元</span></p>
                        <span>起投</span>
                    </li>
                    <li class="col-xs-3 nock1">
                        <div class="nock">
                            <canvas data-status="<?= $val['status'] ?>" data-per="<?= (7 === (int) $val['status']) ? 100 : ($val['finish_rate']) ?>"></canvas>
                        <?php if ($val['status'] == 1) { ?>
                            <div class="column-clock"><span><?= $val['start_desc'] ?></span><?= $val['start'] ?></div>
                        <?php } else if ($val['status'] == 2) { ?>
                            <div class="column-clock column-clock_per"><?= $val['finish_rate'] ?>%</div>
                        <?php } else if ($val['status'] == 7) { ?>
                            <div class="column-clock column-clock_per">成立</div>
                        <?php }else{ ?>
                            <div class="column-clock column-clock_per">100%</div>
                         <?php } ?>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="hidden-xs col-sm-1"></div>
        </a>
    <?php endforeach; ?>


</div>
<!--加载跟多-->
<div class="load">加载更多</div>
<div class="nodata">暂无数据</div>

