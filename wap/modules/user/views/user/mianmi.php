<?php

use yii\helpers\Html;

$this->title = '开通免密支付';
$this->showViewport = false;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/tie-card/css/step.css?v=20170327">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="window.location.href='/user/user'">
            <div><?= Html::encode($this->title) ?></div>
            <a class="rg" href="tel:<?= Yii::$app->params['contact_tel'] ?>"><img src="<?= FE_BASE_URI ?>wap/tie-card/img/phone.png" alt=""></a>
        </div>
    <?php } ?>

    <div class="stepShow">
        <ul class="clearfix stepNum">
            <li class="lf f15 noBorder"><img class="stepok" src="<?= FE_BASE_URI ?>wap/tie-card/img/ok.png" alt=""></li>
            <li class="lf line"></li>
            <li class="lf f15 stepNow">2</li>
            <li class="lf line"></li>
            <li class="lf f15">3</li>
        </ul>
        <ul class="clearfix f12 stepIntro">
            <li class="lf">开通资金托管账户</li>
            <li>开通免密服务</li>
            <li class="rg">绑定银行卡</li>
        </ul>
    </div>
    <div class="message">
        <p class="f15"><img class="icon" src="<?= FE_BASE_URI ?>wap/tie-card/img/icon_01.png" alt=""> 支付密码的短信已发送，请注意查收</p>
        <p class="f15">密码为6位随机数，建议根据短信内容修改密码</p>
    </div>

    <a class="instant f18" href="/user/qpay/binding/umpmianmi">开 通</a>

    <p class="f12 tipsCtn">＊现在将为您开通免密支付功能，方便您进行投资行为</p>
</div>

<script type="text/javascript">
    $(function() {
        var err = '<?= $code ?>';
        var mess = '<?= $message ?>';
        var tourl = '<?= $tourl ?>';
        if('1' === err) {
            toastCenter(mess, function() {
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        }
    })
</script>