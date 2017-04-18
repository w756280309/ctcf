<?php foreach ($vouchers as $voucher) { ?>
    <li class="clearfix" data-index="<?= $voucher['id'] ?>">
        <div class="lf"><p><?= $voucher['goodsType']['name'] ?></p><p><?= $voucher['createTime'] ?></p></div>
        <span class="lf"><i></i></span>
        <button class="rg <?= $voucher['isRedeemed'] ? 'eschanged' : 'eschanging' ?>"><?= $voucher['isRedeemed'] ? '已领取' : '领取' ?></button>
    </li>
<?php } ?>