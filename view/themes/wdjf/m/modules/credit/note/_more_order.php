<?php
use common\utils\StringUtils;
?>

<?php foreach ($orders as $order) { ?>
    <div class="row cre-list-nums">
        <div class="col-xs-4 cre-title"><?= StringUtils::obfsMobileNumber($users[$order['user_id']]['mobile']) ?></div>
        <div class="col-xs-4 cre-title cre-data"><span class="data1"><?= substr($order['createTime'], 0, 10) ?></span><span class="data2"><?= substr($order['createTime'], -8) ?></span></div>
        <div class="col-xs-4 cre-title"><?= StringUtils::amountFormat3(bcdiv($order['principal'], 100, 2)) ?></div>
    </div>
<?php } ?>