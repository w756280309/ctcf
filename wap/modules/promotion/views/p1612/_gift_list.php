<?php
use common\models\promo\Promo1212;
?>

<?php foreach ($rewardList as $record) { $config = Promo1212::getPrizeMessageConfig($record); ?>
    <ul>
        <li class="clearfix">
            <img class="lf" src="<?= $config['pic'] ?>" alt="">
            <p class="lf"><span><?= $config['name'] ?></span><br><span><?= date('Y-m-d H:i', $record->drawAt) ?></span></p>
        </li>
    </ul>
<?php } ?>
