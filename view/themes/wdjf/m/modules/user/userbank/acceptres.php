<?php

use yii\helpers\Html;

$this->title = 'success' === $ret ? '绑卡成功' : '绑卡失败';
$this->showViewport = false;
$backUrl = 'success' === $ret ? '/user/user' : '/user/bank';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/tie-card/css/operate.css?v=20170905">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f19">
            <img class="goback" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png" alt="" onclick="window.location.href='<?= $backUrl ?>'">
            <div><?= Html::encode($this->title) ?></div>
        </div>
    <?php } ?>

    <?php if ('success' === $ret) { ?>
        <img class="sucImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/success.png" alt="">
        <p class="sucMes f17">绑卡申请提交成功</p>
        <div class="message">
            <p class="f15">充值后即可进行投资</p>
            <p class="f15">体验超短期新手专享标，预期年化率<span class="red">10%</span></p>
        </div>
        <a class="instant f18" href="/user/userbank/recharge?backUrl=<?= urlencode(Yii::$app->request->hostInfo.'/user/user') ?>" id="to_recharge">去 充 值</a>
    <?php } else { ?>
        <img class="sucImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/fail.png" alt="">
        <p class="sucMes f17">绑卡申请提交失败</p>
        <a class="instant f18 mg-110" href="/user/bank">重 新 绑 卡</a>
        <p class="f13 color-6 mg-17">遇到问题，请联系客服</p>
        <p class="f13 color-6">客服电话：<a class="color-blue f15" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p>
    <?php } ?>
</div>
<?php if ('success' === $ret) { ?>
    <script type="text/javascript">
        $(function () {
            $('#to_recharge').on('click', function (e) {
                e.preventDefault();

                var toUrl = $(this).attr('href');
                $.get(toUrl, function (data) {
                    if (data.code) {
                        toastCenter(data.message, function () {
                            if ('undefined' !== typeof data.next && data.next) {
                                location.href = data.next;
                            }
                        });
                    } else {
                        location.href = toUrl;
                    }
                });
            })
        })
    </script>
<?php } ?>