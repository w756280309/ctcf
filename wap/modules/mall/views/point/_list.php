<?php

use common\models\mall\PointRecord;
use common\utils\StringUtils;

?>

<?php foreach ($points as $point) { $isIn = PointRecord::TYPE_LOAN_ORDER === $point['ref_type']; ?>
    <div class="clear"></div>
    <div  class="row jiaoyi">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <div class="col-xs-6 lf"><p  class="way"><?= $isIn ? '投资获得' : '兑换商品'; ?></p><p class="revenue">当前积分：<span><?= StringUtils::amountFormat2($point['final_points']) ?></span></p></div>
            <div class="col-xs-6 rg"><p  class="money <?= $isIn ? 'red' : 'green' ?>" ><?= $isIn ? ('+'.StringUtils::amountFormat2($point['incr_points'])) : ('-'.StringUtils::amountFormat2($point['decr_points'])) ?></p><p class="date" ><span class="data1"><?= $point['recordTime'] ?></span></p></div>
        </div>
        <div class="col-xs-1"></div>
    </div>
<?php } ?>