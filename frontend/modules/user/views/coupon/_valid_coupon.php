<div data_count = <?= count($data) ?>>
    <?php foreach($data as $v) { ?>
        <input type="radio" name="couponId" value="<?= $v->id?>" class="coupon_radio">
        <span>¥ <?= $v->couponType->amount ?></span> |
        <span class="coupon_name"><?= $v->couponType->name ?></span> |
        单笔投资满 <?= \Yii::$app->functions->toFormatMoney(rtrim(rtrim($v->couponType->minInvest, '0'), '.')) ?> 元可用 |
        <p>所有项目可用</p>
    <?php }?>
</div>