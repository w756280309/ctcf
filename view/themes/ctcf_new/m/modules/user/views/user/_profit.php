<?php

use common\utils\StringUtils;

?>

<?php foreach ($profits as $profit) { ?>
    <li>
        <div class="lf">
            <p class="f15"><?= $profit->loan->title ?></p>
            <p class="f11"><?= empty($profit->actualRefundTime) ? date('Y-m-d', $profit->refund_time) : substr($profit->actualRefundTime, 0, 10) ?></p>
        </div>
        <div class="rg f15">+<?= StringUtils::amountFormat3($profit->lixi) ?>元</div>
    </li>
<?php } ?>