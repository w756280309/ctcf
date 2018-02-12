<?php

use common\models\code\GoodsType;

?>

<?php foreach ($vouchers as $voucher) { ?>
    <li class="clearfix" data-index="<?= $voucher['id'] ?>">
        <div class="lf"><p><?= $voucher['goodsType']['name'] ?></p><p><?= $voucher['createTime'] ?></p></div>
        <?php if (!$voucher['isRedeemed'] && GoodsType::TYPE_COUPON === intval($voucher['goodsType']['type'])) { ?>
            <button class="rg eschanging">领取</button>
        <?php } elseif ($voucher['isRedeemed']) { ?>
            <button class="rg eschanged">已领取</button>
        <?php } ?>
    </li>
<?php } ?>