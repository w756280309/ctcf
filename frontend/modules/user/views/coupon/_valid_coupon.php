<ul data_count= <?= count($data) ?>>
    <?php foreach ($data as $v) { ?>
        <li class="quan-false">
            <input type="radio" name="couponId" value="<?= $v->id ?>" class="coupon_radio" style="display: none;">
            <div class="quan-left">
                <span>￥</span><?= $v->couponType->amount ?><
            </div>
            <div class="quan-right">
                <div class="quan-right-content">
                    <div><?= $v->couponType->name ?></div>
                    <p>
                        单笔投资满<?= \Yii::$app->functions->toFormatMoney(rtrim(rtrim($v->couponType->minInvest, '0'), '.')) ?>可用</p>
                    <p  class="coupon_name" style="display: none"> 单笔投资满<?= \Yii::$app->functions->toFormatMoney(rtrim(rtrim($v->couponType->minInvest, '0'), '.')) ?>可抵扣<?= $v->couponType->amount ?>元</p>
                    <p>所有项目可用</p>
                    <p>有效期至<?= $v->couponType->useEndDate ?></p>
                </div>
            </div>
            <img class="quan-true" src="/images/deal/quan-true.png" alt="">
        </li>
    <?php } ?>

</ul>