<?php

use yii\helpers\Html;

$this->title = 'success' === $ret ? '绑卡成功' : '绑卡失败';
$this->showViewport = false;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/tie-card/css/operate.css">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>

<div class="flex-content">
    <div class="topTitle f19">
        <div><?= Html::encode($this->title) ?></div>
    </div>

    <?php if ('success' === $ret) { ?>
        <img class="sucImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/success.png" alt="">
        <p class="sucMes f17">绑卡申请提交成功</p>
        <div class="message">
            <p class="f15">充值后即可进行投资</p>
            <p class="f15">体验超短期新手专享标，预期年化率<span class="red">10%</span></p>
        </div>
        <a class="instant f18" href="/user/userbank/recharge">去 充 值</a>
    <?php } else { ?>
        <img class="sucImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/fail.png" alt="">
        <p class="sucMes f17">绑卡申请提交失败</p>
        <a class="instant f18 mg-110" href="javascript:history(-1)">重 新 绑 卡</a>
        <p class="f13 color-6 mg-17">遇到问题，请联系客服</p>
        <p class="f13 color-6">客服电话：<a class="color-blue f15" href="tel:<?= Yii::$app->params['contact_tel'] ?>"><?= Yii::$app->params['contact_tel'] ?></a></p>
    <?php } ?>
</div>

<script type="text/javascript">
    $(function() {
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if ('1' === err) {
            toastCenter(mess, function () {
                if ('' !== tourl) {
                    location.href = tourl;
                }
            });
            return;
        }
    })
</script>
