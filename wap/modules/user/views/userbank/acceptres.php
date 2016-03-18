<?php
$this->title="绑卡受理结果";

$this->registerJsFile('', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>綁卡提交申请成功</div>
        <?php } else { ?>
        <div>綁卡提交申请失败</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi">
     <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div><span>5秒</span>后前往关闭页面</div>
        <?php } else { ?>
        <div>请联系客服: <?= Yii::$app->params['contact_tel'] ?></div>
        <?php } ?>
     </div>
</div>
<?php if ('success' === $ret) { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user/index" class="bind-close1">立即关闭</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<div class="row" id='bind-close2'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/userbank/recharge" class="bind-close1">去充值</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<script>
    $(function(){
        var num=5;
        var t=setInterval(function(){
            num--;
            $('.daojishi .col-xs-12 span').html(num+'秒');
            if(num==0){
                clearInterval(t);
                location.href = '/user/user/index';
            }
        },1000);
    })
</script>
<?php } else { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user/index" class="bind-close1">返回账户</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<?php } ?>
