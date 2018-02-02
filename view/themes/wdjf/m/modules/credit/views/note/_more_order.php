<?php
use common\utils\StringUtils;
use common\utils\SecurityUtils;
?>

<?php foreach ($orders as $order) { ?>
    <div class="row cre-list-nums">
        <div class="col-xs-4 cre-title"><?= StringUtils::obfsMobileNumber(SecurityUtils::decrypt($users[$order['user_id']]['safeMobile'])) ?></div>
        <div class="col-xs-4 cre-title cre-data"><span class="data1"><?= substr($order['createTime'], 0, 10) ?></span><span class="data2"><?= substr($order['createTime'], -8) ?></span></div>
        <div class="col-xs-4 cre-title"><?= StringUtils::amountFormat3(bcdiv($order['principal'], 100, 2)) ?></div>
    </div>
<?php } ?>