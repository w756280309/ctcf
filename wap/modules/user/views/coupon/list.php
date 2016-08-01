<?php
$this->title = '我的代金券';

$this->registerCssFile(ASSETS_BASE_URI . 'css/coupon.css?v=20150520', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'js/coupon-list.js?v=20150708', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI . 'js/couponcode.js', ['depends' => 'wap\assets\WapAsset']);
$this->registerJs('var tp=' . $header->pageCount . ';', 1);
?>

<!--有优惠券的状态  -->
<?php if (!empty($model)) {?>
    <div class="container coupon">
        <a href="javascript:" id="couponcode" class="couponcode">我有兑换码</a>
        <div style="clear:both"></div>
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
                    if (date('Y-m-d') > $val['expiryDate']) {
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
                    <div class="col-xs-8 ticket_time">有效期至<?= $val['expiryDate'] ?></div>
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
<div class="code-mark"></div>
<div class="couponcode-box" id="couponcode-box">
    <form action="/user/couponcode/duihuan" method="post" id="code-forms">
        <h3 class="code-top">领取兑换码<img class="close" src="<?= ASSETS_BASE_URI ?>images/close.png" alt=""></h3>
        <div class="code-content">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
            <div class="code-box">
                <label for="code">兑换码</label>
                <input id="code" class="coupon-code" type="text" maxlength="16" placeholder="请输入代金券兑换码" name="code" autocomplete="off" tabindex="1">
                <div style="clear: both"></div>
                <div class="popUp code_err"></div>
            </div>
            <div style="width: 100%">
                <p class="refer">*兑换码一般从温都金服宣传页、合作网站等获得</p>
                <p class="refer">*必须在有效期内兑换代金券，过期无法兑换</p>
            </div>
        </div>
        <div class="code-success">
            <div class="code-box code-info"><i></i><span>兑换成功!</span></div>
            <div class="code-txt"><p>恭喜您获得了<span id="success-refer"></span></p></div>
        </div>
        <div class="code-bottom">
            <a id="code_submit_button" tabindex="2" style="background: rgb(244, 67, 54);">立即兑换</a>
        </div>
        <input type="text" style="display:none" />
    </form>
</div>
