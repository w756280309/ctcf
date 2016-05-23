<?php
$this->title = '我的代金券';

$this->registerCssFile(ASSETS_BASE_URI . 'css/coupon.css?v=20150520', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'js/coupon-list.js?v=20150520', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
?>

<!--有优惠券的状态  -->
<?php if (!empty($model)) { ?>
    <div class="container coupon">
        <?php
            foreach ($model as $val) :
                $desc = '未使用';
                $div = '';
                $image = 'ok_ticket';
                if ($val['isUsed']) {
                    $desc = '已使用';
                    $div = '<div class="row over_img over_user_img"></div>';
                    $image = 'over_ticket';
                } else {
                    if (date('Y-m-d') > $val['useEndDate']) {
                        $desc = '已过期';
                        $div = '<div class="row over_img over_time_img"></div>';
                        $image = 'over_ticket';
                    }
                }
        ?>
        <div class="box">
            <div class="row coupon_num">
                <img src="<?= ASSETS_BASE_URI ?>images/<?= $image ?>.png" alt="券">
                <div class="row pos_box">
                    <div class="col-xs-2"></div>
                    <div class="col-xs-4 numbers">¥<span><?= rtrim(rtrim(number_format($val['amount'], 2), '0'), '.') ?></span></div>
                    <div class="col-xs-6 right_tip">
                        <div class="a_height"></div>
                        <div class="b_height">
                            <p class="b_h4"><?= $val['name'] ?></p>
                        </div>
                        <div class="c_height">
                            <p class="condition1">单笔投资满<?= $val['minInvestDesc'] ?>可用</p>
                        </div>
                        <div class="d_height"></div>
                        <div class="c_height">
                            <p  class="condition1">所有项目可用</p>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <?= $div ?>
            </div>
            <div class="row gray_time">
                <img src="<?= ASSETS_BASE_URI ?>images/coupon_img.png" alt="底图">
                <div class="row pos_box">
                    <div class="col-xs-8 ticket_time">有效期至<?= $val['useEndDate'] ?></div>
                    <div class="col-xs-4 no-use"><?= $desc ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="load" style="display:block;"></div>
    </div>
<?php } else { ?>
    <!--无优惠券的状态  -->
    <div class="container coupon coupon_none"></div>
<?php } ?>