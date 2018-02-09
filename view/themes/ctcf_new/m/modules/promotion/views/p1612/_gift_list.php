<?php
use common\models\promo\Promo1212;
?>

<ul>
    <?php foreach ($rewardList as $record) { $config = Promo1212::getPrizeMessageConfig($record); ?>
        <li class="clearfix">
            <img class="lf" src="<?= $config['pic'] ?>" alt="">
            <p class="lf"><span><?= $config['name'] ?></span><br><span><?= date('Y-m-d H:i', $record->drawAt) ?></span></p>
        </li>
    <?php } ?>
</ul>
