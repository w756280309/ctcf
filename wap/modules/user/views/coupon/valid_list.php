<?php
$this->title = '可用代金券';

$this->registerCssFile(ASSETS_BASE_URI . 'css/coupon.css', ['depends' => 'frontend\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'js/coupon-valid.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
?>

<div class="container coupon">
    <?php foreach ($coupon as $val) : ?>
        <div class="box">
            <div class="row coupon_num" onclick="location.replace('/order/order?sn=<?= $sn ?>&money=<?= $money ?>&couponId=<?= $val['uid'] ?>')">
                <img src="<?= ASSETS_BASE_URI ?>images/ok_ticket.png" alt="券">
                <div class="row pos_box">
                    <div class="col-xs-2"></div>
                    <div class="col-xs-4 numbers">¥<span><?= rtrim(rtrim(number_format($val['amount'], 2), '0'), '.') ?></span></div>
                    <div class="col-xs-6 right_tip">
                        <div class="a_height"></div>
                        <div class="b_height">
                            <p class="b_h4"><?= $val['name'] ?></p>
                        </div>
                        <div class="c_height">
                            <p class="condition1">单笔投资满<?= rtrim(rtrim(number_format($val['minInvest'], 2), '0'), '.') ?>元可用</p>
                        </div>
                        <div class="d_height"></div>
                        <div class="c_height">
                            <p  class="condition1">所有项目可用</p>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="row gray_time">
                <img src="<?= ASSETS_BASE_URI ?>images/coupon_img.png" alt="底图">
                <div class="row pos_box">
                    <div class="col-xs-8 ticket_time">有效期至<?= $val['useEndDate'] ?></div>
                    <div class="col-xs-4 no-use">未使用</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="load" style="display:block;"></div>
</div>


