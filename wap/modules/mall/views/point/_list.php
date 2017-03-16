<?php

use common\models\mall\PointRecord;
use common\utils\StringUtils;

?>

<?php
foreach ($points as $point) {
    if (PointRecord::TYPE_LOAN_ORDER === $point['ref_type']) {
        $message = '投资获得';
        $isIn = true;
    } elseif  (PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1 === $point['ref_type']) {
        $message = '首投奖励';
        $isIn = true;
    } elseif (PointRecord::TYPE_POINT_FA_FANG === $point['ref_type']) {
        $message = $point['remark'];
        $isIn = true;
    } elseif(PointRecord::TYPE_MALL_INCREASE === $point['ref_type']) {
        $message = $point['remark'];
        $isIn = true;
    } else {
        $isIn = false;
        $message = '兑换商品';
    }
?>
    <div class="clear"></div>
    <div  class="row jiaoyi">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <div class="col-xs-6 lf"><p  class="way"><?= $message ?></p><p class="revenue">当前积分：<span><?= StringUtils::amountFormat2($point['final_points']) ?></span></p></div>
            <div class="col-xs-6 rg"><p  class="money <?= $isIn ? 'red' : 'green' ?>" ><?= $isIn ? ('+'.StringUtils::amountFormat2($point['incr_points'])) : ('-'.StringUtils::amountFormat2($point['decr_points'])) ?></p><p class="date" ><span class="data1"><?= $point['recordTime'] ?></span></p></div>
        </div>
        <div class="col-xs-1"></div>
    </div>
<?php } ?>