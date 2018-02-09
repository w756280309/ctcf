<?php

use common\utils\StringUtils;

?>

<?php foreach ($pointOrders as $pointOrder) { ?>
    <li class="item-qd">
        <?= $pointOrder['recordTime'] ?>
        <span class="rg"><span class="org">+<?= StringUtils::amountFormat2($pointOrder['incr_points']) ?></span>积分</span>
    </li>
<?php } ?>