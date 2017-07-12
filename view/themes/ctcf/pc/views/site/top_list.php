<?php
use common\utils\StringUtils;
?>

<?php $num = 0; foreach ($data as $val) { ++$num; ?>
    <?php if ($num <= 3) { ?>
        <ul>
            <li class="more-bottom-left"><span><?= $num ?></span></li>
            <li class="more-bottom-middle"><?= StringUtils::obfsMobileNumber($val['mobile']) ?></li>
            <li class="more-bottom-right"><?= number_format($val['totalInvest'], 2) ?></li>
        </ul>
    <?php } else { ?>
        <ul>
            <li class="more-bottom-left" style="color: #99999d;line-height: 30px;"><?= $num ?></li>
            <li class="more-bottom-middle"><?= StringUtils::obfsMobileNumber($val['mobile']) ?></li>
            <li class="more-bottom-right" style="color: #99999d;line-height: 30px;"><?= number_format($val['totalInvest'], 2) ?></li>
        </ul>
    <?php } ?>
<?php } ?>