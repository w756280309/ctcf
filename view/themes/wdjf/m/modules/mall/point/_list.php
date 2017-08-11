<?php

use common\models\mall\PointRecord;
use common\utils\StringUtils;

?>

<?php
foreach ($points as $point) {
   ?>
    <div class="clear"></div>
    <div  class="row jiaoyi">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <div class="col-xs-6 lf">
                <p  class="way"><?php if ($point->ref_type == 'point_fa_fang' || $point->ref_type == 'point_batch') {
                        echo $point->remark;
                    } else {
                        echo $point->getTypeName($point->ref_type);
                    } ?>
                <p class="revenue">当前积分：
                    <span><?= StringUtils::amountFormat2($point->final_points) ?></span>
                </p>
            </div>
            <div class="col-xs-6 rg">
                <p  class="money <?= $point->decr_points ? 'green' : 'red' ?>" ><?= $point->getDelta() > 0 ? '+' . $point->getDelta() : $point->getDelta() ?></p>
                <p class="date" ><span class="data1"><?= $point->recordTime ?></span></p>
            </div>
        </div>
        <div class="col-xs-1"></div>
    </div>
<?php } ?>