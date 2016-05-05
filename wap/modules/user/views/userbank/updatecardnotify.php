<?php
$this->title = "换卡申请结果";
$this->backUrl = false;

$this->registerJsFile('', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/mycard.css">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div>换卡申请已成功提交</div>
        <?php } else { ?>
            <div>换卡申请提交失败</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <img src="/images/bind-card-true.png" alt="换卡申请提交成功">
        <?php } else { ?>
            <img src="/images/bind-card-false.png" alt="换卡申请提交失败">
        <?php } ?>
    </div>
</div>
<div class="row daojishi">
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div><span>5秒</span>后前往关闭页面</div>
        <?php } else { ?>
            <div>&nbsp;</div>
        <?php } ?>
    </div>
</div>
<?php if ('success' === $ret) { ?>
    <div class="row" id='bind-close1'>
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <a href="/user/userbank/mycard" class="bind-close1">关闭</a>
        </div>
        <div class="col-xs-3"></div>
    </div>
    <script>
        $(function () {
            var num = 5;
            var t = setInterval(function () {
                num--;
                $('.daojishi .col-xs-12 span').html(num + '秒');
                if (num == 0) {
                    clearInterval(t);
                    location.href = '/user/userbank/mycard';
                }
            }, 1000);
        })
    </script>
<?php } else { ?>
    <div class="row" id='bind-close1'>
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <a href="/user/userbank/updatecard" class="bind-close1">重新提交</a>
        </div>
        <div class="col-xs-3"></div>
    </div>
<?php } ?>